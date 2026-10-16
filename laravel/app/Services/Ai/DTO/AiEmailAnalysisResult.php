<?php

namespace App\Services\Ai\DTO;

class AiEmailAnalysisResult{
    
    public function __construct(
        public ?string $shortSummary,
        public ?string $detailedSummary,
        public ?array $actionNeeded = [],
        public ?string $urgency,
        public ?string $tone,
        public ?string $intent,
        public ?array $secondaryIntents = [],
        public ?array $rawResponse,
        public ?string $modelUsed,
    ) {}

    public static function fromArray(array $data, ?string $modelUsed = "unkown"): self
    {
         return new self(
            rawResponse: $data,
            modelUsed: $modelUsed,
            shortSummary: $data['short_summary'] ?? null,
            detailedSummary: $data['detailed_summary'] ?? null,
            tone: $data['tone'] ?? null,
            intent: $data['intent'] ?? null,
            secondaryIntents: $data['secondary_intents'] ?? [],
            urgency: $data['urgency'] ?? 'low',
            actionNeeded: $data['action_needed'] ?? [],            
        );
    }
}