<?php

namespace App\Services\Ai\Providers;

use App\Models\Email;
use App\Services\Ai\Contracts\AiProviderInterface;
use App\Services\Ai\DTO\AiEmailAnalysisResult;

class FakeAiProvider implements AiProviderInterface
{
    public function analyzeEmail(Email $email, string $text): AiEmailAnalysisResult
    {
        return new AiEmailAnalysisResult(
            summary: 'This is a fake AI summary for testing the email analysis flow.',
            actionNeeded: [
                [
                    'type' => 'reply',
                    'description' => 'Review the email and respond if necessary.',
                ],
            ],
            urgency: 'medium',
            tone: 'formal',
            intent: 'requesting',
            secondaryIntents: ['informative'],
            rawResponse: [
                'provider' => 'fake',
                'email_id' => $email->id,
            ],
            modelUsed: 'fake-ai-v1',
        );
    }
}