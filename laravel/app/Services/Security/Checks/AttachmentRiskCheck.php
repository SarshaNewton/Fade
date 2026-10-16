<?php

namespace App\Services\Security\Checks;

use App\Models\Email;
use App\Services\Security\DTO\SecurityScanResult;

class AttachmentRiskCheck extends BaseRiskCheck{

    public function __construct() {
        $this->config = config('security.attachment');
    }

    protected array $currentScores = [
        'missing_metadata' => 0,
        'archive_extensions' => 0,
        'macro_risk_extensions' => 0,
        'dangerous_extensions' => 0,
        'unexpected_mime_types' => 0
    ];

    public function run(Email $email, SecurityScanResult $result): void{
        $attachments = $email->attachments ?? collect();

        if($attachments->isEmpty()) {
            return;
        }

        foreach($attachments as $attachment){
            $fileName = strtolower($attachment->file_name ?? '');
            $mime = strtolower($attachment->mime ?? '');
            $extension = $this->getExtension($fileName);

            $this->checkMissingMetadata($fileName, $mime, $result);
            $this->checkDangerousExtension($fileName, $extension, $result);
            $this->checkMacroRisk($fileName, $extension, $result);
            $this->checkArchiveRisk($fileName, $extension, $result);
            $this->checkMimeMismatch($fileName, $extension, $mime, $result);
        }
    }
    
    protected function checkMissingMetadata(string $fileName, string $mime, SecurityScanResult $result): void{
        if($fileName === '' || $mime === '' ){
            $this->handleResult($result, 'missing_metadata', 'missing_metadata', 'Attachment is missing file name or MIME type');
        }
    }

    protected function checkDangerousExtension(string $fileName, string $extension, SecurityScanResult $result): void{
        if (in_array($extension, $this->config['dangerous_extensions']['types'], true)) {
            $this->handleResult($result, 'dangerous_extensions', 'dangerous_extensions', "Attachment '{$fileName}' has a dangerous extension: .{$extension}");
        }
    }

    protected function checkMacroRisk(string $fileName, string $extension, SecurityScanResult $result): void{    
        if (in_array($extension, $this->config['macro_risk_extensions']['types'], true)) {
             $this->handleResult($result, 'macro_risk_extensions', 'macro_risk_extensions', "Attachment '{$fileName}' may support macros: .{$extension}");
        }
    }

    protected function checkArchiveRisk(string $fileName, string $extension, SecurityScanResult $result): void{
        if (in_array($extension, $this->config['archive_extensions']['types'], true)) {
             $this->handleResult($result, 'archive_extensions', 'archive_extensions', "Attachment '{$fileName}' is an archive and may hide risky files: .{$extension}");    
        }
    }

    protected function checkMimeMismatch(string $fileName, string $extension, string $mime, SecurityScanResult $result): void {
        if ($extension === '' || $mime === '') {
            return;
        }

        $expectedMimeTypes = $this->config['expected_mime_types']['types'][$extension] ?? [];

        if (empty($expectedMimeTypes)) {
            return;
        }

        if (! in_array($mime, $expectedMimeTypes, true)) {
             $this->handleResult($result, 'unexpected_mime_types', 'unexpected_mime_types', "Attachment '{$fileName}' MIME type '{$mime}' does not match expected type for .{$extension}");    
        }
    }

    private function getExtension(string $fileName): string{
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }   
}