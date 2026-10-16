<?php

namespace App\Services\Mail\Smtp\Providers;

use App\Models\EmailAccount;
use App\Services\Mail\EmailAccountTokenService;
use App\Services\Mail\Smtp\Contracts\SmtpProviderInterface;

class GoogleSmtpProvider implements SmtpProviderInterface{
   
    public function __construct(protected EmailAccountTokenService $tokenService){}

    public function supports(EmailAccount $account): bool{
        return $account->provider == 'google';
    }

    public function buildDsn(EmailAccount $account): string {

        return sprintf(
            'smtp://%s:%s@smtp.gmail.com:587?auth_mode=xoauth2&encryption=tls',
            urlencode($account->email_address),
            urlencode($this->tokenService->getAccessToken($account))
        );
    }
}