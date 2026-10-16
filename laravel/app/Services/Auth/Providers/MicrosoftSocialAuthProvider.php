<?php

namespace App\Services\Auth\Providers;

use Laravel\Socialite\Facades\Socialite;
use App\Services\Auth\Contracts\SocialAuthProviderInterface;

class MicrosoftSocialAuthProvider implements SocialAuthProviderInterface
{
    public function supports(string $provider): bool {
        return strtolower($provider) === 'microsoft';
    }

    public function redirect() {
        return Socialite::driver('microsoft')
            ->scopes([
                'openid',
                'profile',
                'email',
                'offline_access',
                'https://outlook.office.com/IMAP.AccessAsUser.All',
                'https://outlook.office.com/SMTP.Send',
            ])
            ->redirect();
    }

    public function user() {
        return Socialite::driver('microsoft')->stateless()->user();
    }
}