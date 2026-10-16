<?php

namespace App\Services\Security\Checks;

use App\Services\Security\DTO\SecurityScanResult;

abstract class BaseRiskCheck
{
    protected array $config;
    protected array $currentScores = [];

    protected function handleResult(SecurityScanResult $result, string $configKey, string $category, string $reason, float $weight = 1): void {
        // Takes $configKey and $category in the event they have different names
        $scoreGuide = $this->config[$configKey];
        $currentScore = $this->currentScores[$category] ?? 0;

        $base = $scoreGuide['scores']['base'];
        $repeat = $scoreGuide['scores']['repeat'];
        $cap = $scoreGuide['scores']['cap'];

        if ($currentScore >= $cap) {
            return;
        }

        $scoreToAdd = $currentScore === 0 ? $base * $weight : $repeat * $weight;

        $remainingScore = $cap - $currentScore;
        $scoreToAdd = min($scoreToAdd, $remainingScore);

        $this->currentScores[$category] = $currentScore + $scoreToAdd;

        $result->add($scoreToAdd, $category, $reason);
    }
}