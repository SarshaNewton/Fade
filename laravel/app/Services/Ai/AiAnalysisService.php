<?php

namespace App\Services\Ai;

use App\Models\Email;
use App\Models\EmailAiInsight;
use App\Services\Ai\AiSanitizerService;
use App\Services\Ai\Contracts\AiProviderInterface;

class AiAnalysisService{

    public function __construct( protected AiProviderInterface $provider, protected AiSanitizerService $sanitizer) {}

    public function analyzeAndStore(Email $email): EmailAiInsight{
        
        $cleanText = $this->sanitizer->sanitize($email);
        $result = $this->provider->analyzeEmail($email, $cleanText);

        return EmailAiInsight::updateOrCreate(
            [
                'email_id' => $email->id,
                'model_used' => $result->modelUsed,
            ],
            [
                'short_summary' => $result->shortSummary,
                'detailed_summary' => $result->detailedSummary,
                'action_needed' => $result->actionNeeded,
                'urgency' => $result->urgency,
                'tone' => $result->tone,
                'intent' => $result->intent,
                'secondary_intents' => $result->secondaryIntents,
                'raw_response' => $result->rawResponse,
                'updated_at' => now(),
            ]
        );
    }
}