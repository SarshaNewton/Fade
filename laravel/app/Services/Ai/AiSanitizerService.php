<?php

namespace App\Services\Ai;

use App\Models\Email;

class AiSanitizerService{
    private string $userName = '';

    public function sanitize(Email $email): array{
        $body = $email->body_text;
        $this->userName = $email->emailAccount->user->name;

        if (empty($body) && ! empty($email->body_html)) {
            $body = $email->body_html;
        }

        if (empty($body)) {
            $body = $email->body_snippet ?? '';
        }

        $text = [
            'subject' => $this->clean($email->subject ?? ''),
            'body' => $this->clean($body)
        ];

        return $text;
    }

    private function clean($value): string{
            $clean = $this->htmlToText($value);

            // Remove excessive whitespace
            $clean = preg_replace('/\s+/', ' ', $clean);

            // Remove common reply separators
            $clean = preg_replace('/On\s.+?wrote:/i', '', $clean);

            // Remove email signatures
            $clean = preg_replace('/(?:^|\n)\s*(--\s*|Regards,|Best regards,|Kind regards,|Sincerely,|Sincerely Yours,|Kindest Regards)\s*.*$/is', '', $clean);

            // Change sensitive data to [SENSITIVE DATA]
            $clean = $this->redactEverything($clean);

           return trim($clean);
    }
    
    private function redactEverything(string $text): string {
        if (! empty($this->userName)) {
            $quoted = preg_quote($this->userName, '/');
            // allow any whitespace sequence between name parts and match case-insensitively
            $pattern = '/' . preg_replace('/\s+/', '\\s+', $quoted) . '/iu';
            $text = preg_replace($pattern, '[USER_NAME]', $text);
        }

        $text = preg_replace('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', '[EMAIL]', $text);
        
        $text = preg_replace('/https?:\/\/[^\s<>"\']+/i', '[URL]', $text);
        
        $text = preg_replace('/\b(?:\+?\d[\d\s().-]{7,}\d)\b/', '[PHONE_NUMBER]', $text);

        $text = preg_replace('/\b\d{3}[\s.-]\d{4}\b/', '[PHONE_NUMBER]', $text);

        $text = preg_replace('/\b[A-Z]{2}\d{2}[A-Z0-9]{11,30}\b/', '[IBAN]', $text);

        $text = preg_replace('/\b(?:\d[ -]*?){13,19}\b/', '[CARD_NUMBER]', $text);

        $text = preg_replace('/\b(?:order|comandă|comanda|id|receipt|invoice|factură|factura)\s*[:#-]?\s*[A-Z0-9-]{6,}\b/i', '[REFERENCE_ID]', $text);

        $text = preg_replace('/\b[A-Z0-9]{16,}\b/i', '[LONG_IDENTIFIER]', $text);

        return $text;
    }

    private function htmlToText(string $html): string {
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $html);
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html);

        $text = strip_tags($html);

        return trim($text);
    }
}