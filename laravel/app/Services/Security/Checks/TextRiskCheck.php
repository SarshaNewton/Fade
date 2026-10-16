<?php

namespace App\Services\Security\Checks;

use App\Models\Email;
use App\Services\Security\DTO\SecurityScanResult;

class TextRiskCheck extends BaseRiskCheck {
     protected array $currentScores = [
        'urgency_patterns' => 0,
        'credential_patterns' => 0,
        'financial_patterns' => 0,
        'empty_text_image_heavy_email' => 0
    ];

    protected array $weights = [
        'subject' => 1.5,
        'body' => 1.0,
        'html' => 0.8
    ];  

    public function __construct(){
         $this->config = config('security.text');
    }

    public function run(Email $email, SecurityScanResult $result): void {
        
        $rawParts = $this->stripText($email);
        
        $normalizedParts = [];
        $deLeetedParts = [];

        foreach ($rawParts as $part => $value) {
            $textParts = $this->normalizeText($value);

            $normalizedParts[$part] = $textParts;
            $deLeetedParts[$part] = $this->resolveLeetSpeak($textParts);
        }

        $this->checkUrgency($deLeetedParts, $result);
        $this->checkCredentialLanguage($deLeetedParts, $result);
        $this->checkFinancialLanguage($deLeetedParts, $result);

        $this->checkEmptyOrImageFilledEmail($email, $normalizedParts, $result);
    }

    private function stripText(Email $email): array {
        return [
           'subject' => $email->subject ?? '',
           'body' => $email->body_text ?? '',
           'html' => strip_tags($email->body_html ?? '')
        ];
    }

    private function normalizeText(string $text): string {
        if ($text === '') { return ''; }

        // Eensure that the encoding is UTF-8
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Remove 0 width characters 
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);

        // Convert to lower case using the multibyte safe method 👍
        $text = mb_strtolower($text, 'UTF-8');
        
        // Replace special characters with a space
        $text = preg_replace('/[^\p{L}\p{N}\s@._:\/\-]/u', ' ', $text);

        // Replace romanian characters with standard ones
        $text = strtr($text, [
            'ă' => 'a',
            'â' => 'a',
            'î' => 'i',
            'ș' => 's',
            'ş' => 's',
            'ț' => 't',
            'ţ' => 't',
        ]);

        // Concatanate spaces into 1
        $text = preg_replace('/\s+/u', ' ', $text);

        // trim trailing white spaces
        return trim($text);
    } 

    private function resolveLeetSpeak(string $text): string {
        if ($text === '') { return ''; }

        $text = strtr($text, [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '$' => 's',
        ]);

        // Aggressively remove special characters compared to normalize text
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }

    private function checkUrgency(array $textParts, SecurityScanResult $result): void{
        $message = 'Urgency based language detected in';
        $this->calculateResult('urgency_patterns', $textParts, $message, $result);
    }

    private function checkCredentialLanguage(array $textParts, SecurityScanResult $result): void{
        $message = 'Credential or account verification language detected in';
        $this->calculateResult('credential_patterns', $textParts, $message, $result);

    }

    private function checkFinancialLanguage(array $textParts, SecurityScanResult $result): void {
        $message = 'Financially sensitive language detected in';
        $this->calculateResult('financial_patterns', $textParts, $message, $result);
    }

    private function checkEmptyOrImageFilledEmail(Email $email, array $normalizedParts, SecurityScanResult $result): void{
        $textLength = 0;

        foreach($normalizedParts as $part) {
            $textLength +=mb_strlen(trim($part));
        }

        $html = $email->body_html ?? '';

        $imageCount = preg_match_all('/<img\b/i', $html);
        $linkCount = preg_match_all('/<a\b/i', $html);

        if($textLength < 30){
            $this->handleResult($result, 'empty_text_image_heavy_email','empty_text_image_heavy_email', 'Email contains very little text.');
            return;
        }

        if($imageCount > 0 && $textLength < 80) {
            $this->handleResult($result, 'empty_text_image_heavy_email','empty_text_image_heavy_email', 'Email relies heavily on images with little text.');
            return;
        }

        if($linkCount > 3 && $textLength < 100){
            $this->handleResult($result, 'empty_text_image_heavy_email','empty_text_image_heavy_email', 'Email contains multiple links with little context.');
            return;
        }
        return;
    }

    /* HELPERS */

    private function containsPattern(array $textParts, string $pattern): array{
        $matchedParts = [];

        foreach ($textParts as $part => $text) {
            if (preg_match($pattern, $text)) {
                $matchedParts[] = $part;
            }
        }

        return $matchedParts;
    }

    private function calculateResult(string $configKey, array $textParts, string $message, SecurityScanResult $result): void{
      
        foreach ($this->config[$configKey]['types'] as $pattern) {
            $matchedPart = $this->containsPattern($textParts, $pattern);
          
            foreach($matchedPart as $part){
                $specificMessage = $message . " email {$part}";
                $this->handleResult($result, $configKey, $configKey, $specificMessage, $this->weights[$part] ?? 1.0);
            }   
        }
    }
}