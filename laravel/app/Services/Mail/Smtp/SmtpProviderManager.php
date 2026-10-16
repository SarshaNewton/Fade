<?php

namespace App\Services\Mail\Smtp;

use App\Models\EmailAccount;
use App\Services\Mail\Smtp\Providers\GoogleSmtpProvider;
use App\Services\Mail\Smtp\Providers\MicrosoftSmtpProvider;
use App\Services\Mail\Smtp\Contracts\SmtpProviderInterface;

class SmtpProviderManager {
   
    protected array $providers;

     public function __construct(){
        $this->providers = [
            app(GoogleSmtpProvider::class),
            app(MicrosoftSmtpProvider::class)
        ];
    }

    public function for(EmailAccount $account): SmtpProviderInterface {
        foreach ($this->providers as $provider) {
            if ($provider->supports($account)) {
                return $provider;
            }
        }
        throw new \Exception('Unsupported provider');
    }
}