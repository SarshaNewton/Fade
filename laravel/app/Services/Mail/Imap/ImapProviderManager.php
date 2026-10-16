<?php

namespace App\Services\Mail\Imap;

use App\Models\EmailAccount;
use App\Services\Mail\Imap\Providers\GoogleImapProvider;
use App\Services\Mail\Imap\Providers\MicrosoftImapProvider;
use App\Services\Mail\Imap\Contracts\ImapProviderInterface;

class ImapProviderManager {
    protected array $providers;

    public function __construct() {
        $this->providers = [
            app(GoogleImapProvider::class),
            app(MicrosoftImapProvider::class)
        ];
    }

    public function for(EmailAccount $account): ImapProviderInterface {
        foreach ($this->providers as $provider) {
            if ($provider->supports($account)) {
                return $provider;
            }
        }

        throw new \Exception("Unsupported provider: {$account->provider}");
    }
}