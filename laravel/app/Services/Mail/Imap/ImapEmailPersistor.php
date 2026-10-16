<?php

namespace App\Services\Mail\Imap;

use App\Models\Email;
use App\Models\Mailbox;
use App\Models\Recipient;
use App\Models\EmailAccount;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\ClientManager;

class ImapEmailPersistor{
    
    public function save(EmailAccount $account, Mailbox $mailbox, mixed $message): Email {
        $uid = $message->getUid();
        $from = $message->getFrom()[0] ?? null;

        $date = $message->getDate();

        if ($date) {
            $date = Carbon::parse($date)->setTimezone('UTC');
        }

        $bodyHtml = $message->getHTMLBody();
        $bodyText = $message->getTextBody();

        $snippet = Str::limit($this->makeSnippet($bodyText, $bodyHtml), 150);

        $isSeen = $message->hasFlag('Seen');
        $isFlagged = $message->hasFlag('Flagged');

        $header = $message->getHeader();
        $rawHeaders = $header->raw ?? null;
        $headers = parseRawHeaders($rawHeaders);

        $cleanHtmlsubject = $this->decodeMimeHeader(
            $this->stringValue($message->getSubject())
        );

        $fromName = $this->decodeMimeHeader(
            $this->stringValue(optional($from)->personal)
        );

        $email = Email::updateOrCreate(
            [
                'email_account_id' => $account->id,
                'imap_uid' => $uid,
            ],
            [
                'mailbox_id' => $mailbox->id,
                'direction' => 'inbound',

                'subject' => $cleanHtmlsubject,
                'body_snippet' => $snippet ?: null,
                'body_text' => $bodyText ?: null,
                'body_html' => $bodyHtml ?: null,

                'from_name' => $fromName ?: null,
                'from_email_address' => optional($from)->mail,

                'date_header' => $date,
                'received_at' => $date,
                'sent_at' => $date,

                'is_unread' => ! $isSeen,
                'is_starred' => $isFlagged,

                'provider_id' => (string) $uid,
                'remote_thread_id' => (string) $uid,
                'message_id_header' => (string) $uid,

                'headers' => $headers ?: null,
                'raw_headers' => $rawHeaders ?: null,

                'delivery_status' => null,
            ]
        );

        $this->syncRecipients($email, $message);

        return $email;
    }

    private function syncRecipients(Email $email, mixed $message): void {
        $email->recipients()->delete();
                
        $this->saveRecipientGroup($email, $message->getTo(), 'to');
        $this->saveRecipientGroup($email, $message->getCc(), 'cc');
        $this->saveRecipientGroup($email, $message->getBcc(), 'bcc');
    }

    private function saveRecipientGroup(Email $email, mixed $addresses, string $type): void {
        foreach (($addresses?->all() ?? []) as $address) {
            Recipient::create([
                'email_id' => $email->id,
                'type' => $type,
                'name' => $address->personal ?? null,
                'email_address' => $address->mail ?? null,
            ]);
        }
    }

    private function decodeMimeHeader(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        /*
         * Only attempt MIME decoding when the value resembles an
         * RFC 2047 encoded header, for example:
         *
         * =?UTF-8?B?...?=
         * =?UTF-8?Q?...?=
         */
        if (! str_contains($value, '=?')) {
            return trim($value);
        }

        if (function_exists('iconv_mime_decode')) {
            $decoded = iconv_mime_decode(
                $value,
                ICONV_MIME_DECODE_CONTINUE_ON_ERROR,
                'UTF-8'
            );

            if ($decoded !== false) {
                return trim($decoded);
            }
        }

        if (function_exists('mb_decode_mimeheader')) {
            return trim(mb_decode_mimeheader($value));
        }

        return trim($value);
    }

    private function makeSnippet(?string $bodyText, ?string $bodyHtml): string{
        $cleanHtml = $bodyHtml ?? '';

        $cleanHtml = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $cleanHtml);
        $cleanHtml = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $cleanHtml);
        $cleanHtml = preg_replace('/<head\b[^>]*>.*?<\/head>/is', '', $cleanHtml);

        $htmlText = html_entity_decode(strip_tags($cleanHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $textBody = html_entity_decode($bodyText ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $rawBody = filled(trim($htmlText)) ? $htmlText : $textBody;

        $normalizedBody = $rawBody;
        $normalizedBody = preg_replace('/\/\*.*?\*\//s', '', $normalizedBody);
        $normalizedBody = preg_replace('/\{[^{}]*:[^{}]*;?[^{}]*\}/', '', $normalizedBody);
        $normalizedBody = preg_replace('/\b[a-zA-Z-]+\s*:\s*[^;]+;/', '', $normalizedBody);
        $normalizedBody = preg_replace('/\s+/', ' ', $normalizedBody);
        $normalizedBody = trim($normalizedBody);

        return $normalizedBody ?: '';
    }

     private function stringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, 'toString')) {
            return $value->toString();
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return null;
    }
}
