<?php

namespace App\Services\Security;

use App\Models\Email;
use App\Models\Mailbox;
use App\Models\EmailSecurityScan;

use App\Services\Mail\MailboxService;
use App\Services\Security\EmailSecurityScanner;
use App\Services\Security\WorkspaceThreatsService;

class EmailScanService{
    public function __construct(protected EmailSecurityScanner $scanner, protected MailboxService $mailboxService, protected WorkspaceThreatsService $threatService) {}

    public function scanAndStore($email){
        $scanRecord = EmailSecurityScan::updateOrCreate(
            ['email_id' => $email->id],
            [
                'status' => 'scanning',
                'fail_reason' => null,
            ]
        );

        $scanResult = $this->scanner->scan($email);

        $scanRecord->update([
            'score' => $scanResult->score,
            'risk' => $scanResult->getCategory(),
            'security_reasons' => $scanResult->reasons,
            'status' => 'completed',
            'scanned_at' => now(),
        ]);

        if ($scanResult->score >= config('security.spam_threshold')) {
            $spamMailbox = $this->mailboxService->ensureSpambox($email->emailAccount);

            $email->update([
                'mailbox_id' => $spamMailbox->id,
            ]);

            $this->threatService->save($scanRecord->fresh());
        }

        return $scanRecord;
    }

    public function markFailed(string $emailId, \Throwable $e): EmailSecurityScan
    {
        return EmailSecurityScan::updateOrCreate(
            ['email_id' => $emailId],
            [
                'status' => 'failed',
                'fail_reason' => $e->getMessage(),
            ]
        );
    }
}