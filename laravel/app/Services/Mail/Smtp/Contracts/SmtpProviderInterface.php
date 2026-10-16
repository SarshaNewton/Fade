<?php

namespace App\Services\Mail\Smtp\Contracts;

use App\Models\EmailAccount;
use App\Services\Mail\EmailAccountTokenService;

interface SmtpProviderInterface{
    public function supports(EmailAccount $account): bool;
    public function buildDsn(EmailAccount $account):string;
}