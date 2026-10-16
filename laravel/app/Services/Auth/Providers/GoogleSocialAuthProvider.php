<?php

namespace App\Services\Auth\Providers;

use Laravel\Socialite\Facades\Socialite;
use App\Services\Auth\Contracts\SocialAuthProviderInterface;

class GoogleSocialAuthProvider implements SocialAuthProviderInterface {
    
    public function supports(string $provider): bool {
        return strtolower($provider) === 'google';
    }

    public function redirect() {
        return Socialite::driver('google')
            ->stateless()
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://mail.google.com/',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent',
                'include_granted_scopes' => 'true',
            ])
            ->redirect();
    }

    public function user(){
        return Socialite::driver('google')->stateless()->user();
    }
}