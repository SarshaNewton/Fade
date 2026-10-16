<?php

namespace App\Services\Mail\Imap;

use App\Models\EmailAccount;
use App\Services\Mail\Imap\ImapClient;
use App\Services\Mail\EmailAccountTokenService;

class InboxSyncService{

    protected ImapClient $imapClient;
    protected EmailAccountTokenService $tokenService;

    public function __construct(ImapClient $imapClient, EmailAccountTokenService $tokenService){
        $this->imapClient = $imapClient;
        $this->tokenService = $tokenService;       
    }

    public function sync(EmailAccount $account): array{
        //Refresh token if neccessary
        $account = $this->tokenService->ensureValidAccessToken($account);
        //Then sync!
        return $this->imapClient->syncInbox($account, 'INBOX');
    }
}
