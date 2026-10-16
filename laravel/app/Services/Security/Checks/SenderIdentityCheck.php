<?php

namespace App\Services\Security\Checks;

use App\Models\Email;
use App\Services\Security\DTO\SecurityScanResult;

class SenderIdentityCheck extends BaseRiskCheck{
    protected array $currentScores = [
        'missing_sender' => 0,
        'suspicious_display_name' => 0
    ];

    public function __construct(){
         $this->config = config('security.sender');
    }

    public function run(Email $email, SecurityScanResult $result): void {
        $fromEmail = $this->extractEmail($email->from_email_address ?? null);
        $fromName = strtolower($email->from_name ?? '');

        if(!$fromEmail) {
            $this->handleResult($result, 'missing_sender', 'missing_sender', 'Email is missing a valid sender address');
            return;
        }

        $this->checkSuspiciousDisplayName($fromEmail, $fromName, $result);
    }

    private function checkSuspiciousDisplayName(string $fromEmail, string $fromName, SecurityScanResult $result): void {
        if($fromName === ''){
            return;
        }

        $fromDomain = $this->getDomain($fromEmail);

        if(!$fromDomain){
            return;
        }

        foreach($this->config['trusted_brands'] ?? [] as $brand => $officialDomains){
            $brand = strtolower($brand);

            if (!str_contains($fromName, $brand)) {
                continue;
            }

            if (!in_array($fromDomain, $officialDomains, true)) {
                $this->handleResult($result, 'suspicious_display_name', 'suspicious_display_name', "Sender display name mentions {$brand}, but the email comes from {$fromDomain}.");
            }
        }
    }

    private function extractEmail(?string $value): ?string{
        if (!$value) {
            return null;
        }

        if (preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $value, $matches)) {
            return strtolower($matches[0]);
        }

        return null;
    }

    private function getDomain(string $email): ?string{
        $parts = explode('@', $email);

        return $parts[1] ?? null;
    }
}