<?php

namespace App\Services\Security\DTO;

class SecurityScanResult{

    public function __construct(public float $score = 0.0, public array $reasons = []){}

    public function add(float $points, string $category, string $reason): void {
        $this->score += $points;
        // Ensure the category holds an array of reasons and append the new reason
        if (!isset($this->reasons[$category])) {
            $this->reasons[$category] = [];
        }

        if (!in_array($reason, $this->reasons[$category], true)) {
            $this->reasons[$category][] = $reason;
        }
    }

    public function getCategory(): string {
        return match(true) {
            $this->score >= 100 => 'critical',
            $this->score >= 70 => 'high',
            $this->score >= 40 => 'medium',
            $this->score >= 20 => 'low',
            default => 'minimal'
        };
    }

    public function toArray(): array {
        return [
            'score' => $this->score,
            'score_display' => round($this->score, 1),
            'category' => $this->getCategory(),
            'reasons' => $this->reasons,
        ];
    }
}