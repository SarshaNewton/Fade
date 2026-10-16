<?php

namespace App\Services\Mail\Imap\Providers;

use App\Models\EmailAccount;
use App\Services\Mail\EmailAccountTokenService;
use App\Services\Mail\Imap\Contracts\ImapProviderInterface;

class GoogleImapProvider implements ImapProviderInterface {
    
    public function __construct(protected EmailAccountTokenService $tokenService){}

    public function supports(EmailAccount $account): bool {
        return $account->provider === 'google';
    }

    public function getConfig(EmailAccount $account): array {
        return [
            'host' => 'imap.gmail.com',
            'port' => 993,
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => $account->email_address,
            'password' => $this->tokenService->getAccessToken($account),
            'protocol' => 'imap',
            'authentication' => 'oauth',
        ];
    }
}