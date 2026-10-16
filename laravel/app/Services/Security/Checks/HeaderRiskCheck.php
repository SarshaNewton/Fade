<?php

namespace App\Services\Security\Checks;

use App\Models\Email;
use App\Services\Security\DTO\SecurityScanResult;

class HeaderRiskCheck extends BaseRiskCheck{
    protected array $currentScores = [
        'missing_authentication_results' => 0,
        'spf_fail' => 0,
        'dkim_fail' => 0,
        'dmarc_fail' => 0,
        'dmarc_fail_arc_pass' => 0,
        'reply_to_mismatch' => 0,
        'return_path_mismatch' => 0,
    ];

    public function __construct() { $this->config = config('security.header');}

    public function run(Email $email, SecurityScanResult $result): void{
        $headers = $this->extractHeaders($email);

        if(empty($headers)){
            $this->handleResult($result, 'missing_authentication_results', 'missing_authentication_results', 'Email headers are missing, so authentication results could not be checked');
            return;
        }

        $authResults = $this->getHeaderValue($headers, 'authentication_results');

        if (!$authResults) {
            $this->noAuthenticationResults($result);
        } else {
            $this->checkSpf($authResults, $result);
            $this->checkDkim($authResults, $result);
            $this->checkDmarc($authResults, $result);
        }

        $this->checkSenderHeaderMismatch($email, $headers, $result);
    }

    private function checkSpf(string $authResults, SecurityScanResult $result): void{      
        if (str_contains($authResults, 'spf=fail') || str_contains($authResults, 'spf=softfail')) {
            $this->handleResult($result, 'spf_fail', 'spf_fail', 'SPF authentication failed or soft-failed');
        }
    }

    private function checkDkim(string $authResults, SecurityScanResult $result): void{
        if (str_contains($authResults, 'dkim=fail') || str_contains($authResults, 'dkim=permerror') || str_contains($authResults, 'dkim=temperror')) {
            $this->handleResult($result, 'dkim_fail', 'dkim_fail', 'DKIM authentication failed or returned an error.');
        }
    }
    
    private function checkDmarc(string $authResults, SecurityScanResult $result): void{
        if (str_contains($authResults, 'dmarc=fail') || str_contains($authResults, 'dmarc=permerror') || str_contains($authResults, 'dmarc=temperror')) {
            if(str_contains($authResults, 'arc=pass')){
                $this->handleResult($result, 'dmarc_fail_arc_pass', 'dmarc_fail_arc_pass', 'DMARC authentication failed or returned an error.');
            } else {
                $this->handleResult($result, 'dmarc_fail', 'dmarc_fail', 'DMARC authentication failed or returned an error.');
            }
        }
    }

    private function checkSenderHeaderMismatch(Email $email, array $headers, SecurityScanResult $result): void{
        $fromEmail = $this->extractEmail($email->from_email_address ?? $headers['from'] ?? null);
        $replyTo = $this->extractEmail($this->getHeaderValue($headers, 'reply_to'));
        $returnPath = $this->extractEmail($this->getHeaderValue($headers, 'return_path'));

        if(!$fromEmail){
            return;
        }

        $fromDomain = $this->getDomain($fromEmail);

        if(!$fromDomain){
            return;
        }

        if($replyTo){
            $replyToDomain = $this->getDomain($replyTo);

            if($replyToDomain && $replyToDomain !== $fromDomain){
                $this->handleResult($result, 'reply_to_mismatch', 'reply_to_mismatch', "Reply-To domain {$replyToDomain} does not match sender domain {$fromDomain}");
            }
        }

        if($returnPath){
            $returnPathDomain = $this->getDomain($returnPath);

            if($returnPathDomain && $returnPathDomain !== $fromDomain){
                $this->handleResult($result, 'return_path_mismatch', 'return_path_mismatch', "Return-Path domain {$returnPathDomain} does not match sender domain {$fromDomain}");
            }
        }
    }

    private function noAuthenticationResults(SecurityScanResult $result) :void{
        $this->handleResult($result, 'missing_authentication_results', 'missing_authentication_results', 'Authentication-Results header is missing');
    }

    
    /* HELPERS */

    private function extractHeaders(Email $email){
        if ($email->raw_headers) {
            return parseRawHeaders($email->raw_headers);
        }

        $headers = $email->headers ?? [];

        if (is_string($headers)) {
            $headers = json_decode($headers, true) ?? [];
        }

        if (!is_array($headers)) {
            return [];
        }

        return array_change_key_case($headers, CASE_LOWER);
    }

    private function getHeaderValue(array $headers, string $key): ?string{
        $key = strtolower($key);

        $possibleKeys = [
            $key,
            str_replace('_', '-', $key),
            str_replace('-', '_', $key),
        ];

        foreach ($possibleKeys as $possibleKey) {
            if (isset($headers[$possibleKey])) {
                return is_array($headers[$possibleKey])
                    ? implode(' ', $headers[$possibleKey])
                    : (string) $headers[$possibleKey];
            }
        }

        return null;
    }

    private function extractEmail(?string $value): ?string{
        if(!$value){
            return null;
        }

        if (preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $value, $matches)) {
            return strtolower($matches[0]);
        }

        return null;
    }

    private function getDomain(string $email): ?string{
        $parts = explode('@', $email);

        return isset($parts[1]) ? strtolower($parts[1]) : null;
    }


}