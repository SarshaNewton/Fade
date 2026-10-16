<?php

namespace App\Services\Mail\Smtp;

use App\Models\Email;
use App\Models\EmailAccount;
use App\Services\Mail\MailboxService;
use App\Services\Mail\Smtp\SmtpClient;

class SendEmailService {
    
    protected MailboxService $mailboxService;

    public function __construct(MailboxService $mailboxService)
    {
        $this->mailboxService = $mailboxService;
    }

    public function send(EmailAccount $account, Email $draft): Email{
        
        $outbox = $this->mailboxService->ensureOutbox($draft->emailAccount);
        $draft->update([
            'mailbox_id' => $outbox->id,
            'delivery_status' => 'pending',
        ]);

        $client = new SmtpClient($account);
        $client->send($draft);

        $sentMailbox = $this->mailboxService->ensureSentBox($account);

        $draft->update([
            'mailbox_id' => $sentMailbox->id,
            'delivery_status' => 'sent',
            'sent_at' => now()
        ]);

        return $draft->refresh();
    }
}