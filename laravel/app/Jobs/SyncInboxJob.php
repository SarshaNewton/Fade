<?php

namespace App\Jobs;

use App\Jobs\ScanEmailJob;
use App\Models\EmailAccount;
use App\Jobs\AiAnalyzeEmailJob;
use App\Services\Mail\Imap\InboxSyncService;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncInboxJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $emailAccountId;

    public function __construct(string $emailAccountId)
    {
        $this->emailAccountId = $emailAccountId;
    }

    public function handle(InboxSyncService $syncService): void{
        $account = EmailAccount::findOrFail($this->emailAccountId);
        $syncedEmailIds = $syncService->sync($account);
            
        $account->refresh();
        $account->update([
            'last_synced_at' => now(),
        ]);

        try {
            $scanJobs = collect($syncedEmailIds)
                ->unique()
                ->map(fn ($emailId) => new ScanEmailJob((string) $emailId))
                ->toArray();

            if (!empty($scanJobs)) {
                Bus::batch($scanJobs)
                    ->name('Security scan after inbox sync')
                    ->allowFailures()
                    ->dispatch();
            }
            
        } catch (\Throwable $e) {
            logger('SyncInboxJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; 
        }

        try{
            $aiAnalyzeJobs = collect($syncedEmailIds)
                ->unique()
                ->map(fn ($emailId) => new AiAnalyzeEmailJob((string) $emailId))
                ->toArray();

            if (!empty($aiAnalyzeJobs)) {
                Bus::batch($aiAnalyzeJobs)
                    ->name('Ai analyze after inbox sync')
                    ->allowFailures()
                    ->dispatch();
            }
            
        } catch(\Throwable $e){
            logger('AiAnalyzeInboxJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; 
        }
    }
}
