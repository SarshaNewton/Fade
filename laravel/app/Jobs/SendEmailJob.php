<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\EmailAccount;
use App\Services\Mail\MailboxService;
use App\Services\Mail\Smtp\SendEmailService;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public function __construct(public string $accountId, public string $draftId) {}

    public function handle(SendEmailService $sendEmailService): void {
       
        $account = EmailAccount::findOrFail($this->accountId);
        $draft = Email::findOrFail($this->draftId);

        $sendEmailService->send($account, $draft);
    }

    public function failed(\Throwable $exception): void {
        $draft = Email::find($this->draftId);

        if ($draft) {
            $draft->update([
                'delivery_status' => 'failed',
            ]);
        }

        $mailboxService = app(MailboxService::class);
        $outbox = $mailboxService->ensureOutbox($draft->emailAccount);

        $draft->update([
            'mailbox_id' => $outbox->id,
        ]);

        \Log::error('Queued email send failed', [
            'draft_id' => $this->draftId,
            'account_id' => $this->accountId,
            'message' => $exception->getMessage(),
        ]);
    }
}
