<?php

namespace App\Services\Mail\Imap\Contracts;

use App\Models\EmailAccount;

interface ImapProviderInterface{
    public function supports(EmailAccount $account): bool;
    public function getConfig(EmailAccount $account): array;
}