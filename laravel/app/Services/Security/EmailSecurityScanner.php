<?php

namespace App\Services\Security;

use App\Models\Email;
use App\Services\Security\Checks\LinkRiskCheck;
use App\Services\Security\Checks\TextRiskCheck;
use App\Services\Security\Checks\HeaderRiskCheck;
use App\Services\Security\DTO\SecurityScanResult;
use App\Services\Security\Checks\AttachmentRiskCheck;
use App\Services\Security\Checks\SenderIdentityCheck;

class EmailSecurityScanner{
    
    public function __construct(
        protected TextRiskCheck $textRiskCheck,
        protected LinkRiskCheck $linkRiskCheck,
        protected SenderIdentityCheck $senderIdentityCheck,
        protected AttachmentRiskCheck $attachmentRiskCheck,
        protected HeaderRiskCheck $headerRiskCheck,
    ) {}

    public function scan(Email $email): SecurityScanResult{
        $result = new SecurityScanResult();

        $this->textRiskCheck->run($email, $result);
        $this->linkRiskCheck->run($email, $result);
        $this->senderIdentityCheck->run($email, $result);
        $this->attachmentRiskCheck->run($email, $result);
        $this->headerRiskCheck->run($email, $result);

        return $result;
    }
}