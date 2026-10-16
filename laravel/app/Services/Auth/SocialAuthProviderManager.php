<?php

namespace App\Services\Auth;

use App\Services\Auth\Providers\GoogleSocialAuthProvider;
use App\Services\Auth\Providers\MicrosoftSocialAuthProvider;
use App\Services\Auth\Contracts\SocialAuthProviderInterface;
use InvalidArgumentException;

class SocialAuthProviderManager
{
    public function __construct(
        protected GoogleSocialAuthProvider $googleProvider,
        protected MicrosoftSocialAuthProvider $microsoftProvider
    ) {}

    public function for(string $provider): SocialAuthProviderInterface
    {
        $provider = strtolower($provider);

        $providers = [
            $this->googleProvider,
            $this->microsoftProvider,
        ];

        foreach ($providers as $authProvider) {
            if ($authProvider->supports($provider)) {
                return $authProvider;
            }
        }

        throw new InvalidArgumentException("Unsupported social auth provider: {$provider}");
    }
}