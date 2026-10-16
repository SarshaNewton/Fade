<?php

namespace App\Services\Mail;

use App\Models\Email;
use App\Models\Recipient;
use App\Models\EmailAccount;
use App\Services\Mail\MailboxService;

use Illuminate\Support\Str;

class DraftService{

    public function __construct(
        protected MailboxService $mailbox
    ) {}

    public function saveDraft(EmailAccount $account, array $data, $user, ?Email $draft = null): Email{

        $mailbox = $this->mailbox->ensureDraftbox($account);

        $recipients = [];
        
        foreach ([Recipient::TYPE_TO, Recipient::TYPE_CC, Recipient::TYPE_BCC] as $type) {
            foreach ($data[$type] ?? [] as $email) {
                $email = trim($email);

                if ($email !== '') {
                    $recipients[] = [
                        'type' => $type,
                        'email_address' => $email,
                    ];
                }
            }
        }

        $bodyText = $data['body_text'] ?? null;
        $bodyHtml = $data['body_html'] ?? null;

        $rawBody = $bodyText ?: strip_tags($bodyHtml ?? '');
        $normalizedBody = trim(preg_replace('/\s+/', ' ', $rawBody));
        $snippet = Str::limit($normalizedBody, 150);

        $draft = Email::updateOrCreate([
            'id' => $draft?->id
        ],
        [
            'email_account_id' => $account->id,
            'mailbox_id' => $mailbox->id,
            
            'direction' => 'outbound',
            'delivery_status' => 'draft',

            'subject' => $data['subject'] ?? null,
            'body_snippet' => $snippet,
            'body_text' => $bodyText,
            'body_html' => $bodyHtml,

            'from_name' => $user->name,
            'from_email_address' => $account->email_address,

            'sent_at' => null,
        ]);

        // Delete each recipient on save then resave them
        $draft->recipients()->delete();

        foreach ($recipients as $recipient) {
            $draft->recipients()->create([
                'type' => $recipient['type'],
                'email_address' => $recipient['email_address'],
            ]);
        }

        return $draft->load('recipients');
    }
}