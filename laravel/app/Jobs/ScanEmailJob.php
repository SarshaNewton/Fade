<?php

namespace App\Jobs;

use App\Models\Email;
use App\Services\Security\EmailScanService;


use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScanEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public function __construct(public string $emailId){}

    public function handle(EmailScanService $emailScanService): void {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $email = Email::with('attachments')->find($this->emailId);

        if (!$email) {
            return;
        }

        $scan = $emailScanService->scanAndStore($email);

    }

    public function failed(\Throwable $e): void {
        app(EmailScanService::class)->markFailed($this->emailId, $e);

        logger('ScanEmailJob failed', [
            'email_id' => $this->emailId,
            'error' => $e->getMessage(),
        ]);
    }
}
