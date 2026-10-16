<?php

namespace App\Jobs;

use App\Models\Email;
use App\Services\Ai\AiAnalysisService;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AiAnalyzeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public function __construct(public string $emailId){}

    public function handle(AiAnalysisService $analyzer): void {
        if ($this->batch()?->cancelled()) {
            return;
        }

         $email = Email::find($this->emailId);

        if (!$email) {
            return;
        }

        $analyze = $analyzer->analyzeAndStore($email);

    }
}
