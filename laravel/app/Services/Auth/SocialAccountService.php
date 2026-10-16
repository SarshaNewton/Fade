<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\SocialAccount;

class SocialAccountService{
    // Save or update the social account
    public function upsert(User $user, string $provider, $providerUser, ?string $email): void{
        SocialAccount::updateOrCreate(
            [
                'provider' => $provider,
                'provider_user_id' => $providerUser->getId()
            ],[
                'user_id' => $user->id,
                'provider_email' =>$email
            ]
        );
    }

    // Save or update the social account for the pending 
    public function upsertPending(User $user, array $pending): void {
        SocialAccount::updateOrCreate(
            [
                'provider' => $pending['provider'],
                'provider_user_id' => $pending['provider_user_id']
            ],[
                'user_id' => $user->id,
                'provider_email' =>$pending['provider_email']
            ]
        );
    }
}