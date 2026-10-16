<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\EmailAccount;
use App\Models\UserPreference;
use App\Services\Mail\MailboxService;
use function isSupportedMailProvider;
use App\Services\Mail\EmailAccountTokenService;

use Illuminate\Support\Facades\Crypt;

class EmailAccountService {
        
    public function __construct(protected MailboxService $mailboxService, protected EmailAccountTokenService $tokenService) {}

    // Get the deets from the provider
    public function syncFromProviderUser(User $user, string $provider, $providerUser, string $email): EmailAccount {
        if (!isSupportedMailProvider($provider)) {
            return null;
        }

        // Encrypt the access token if given
        $accessToken = isset($providerUser->token)
            ? Crypt::encryptString($providerUser->token)
            : null;

        $expiresAt = isset($providerUser->expiresIn)
            ? now()->addSeconds($providerUser->expiresIn)
            : null;

        // Put data in updateData array so that refresh_token can only be added if it's provided
        // Prevents null encryptions
        $updateData = [
            'access_token' => $accessToken,
            'expires_at' => $expiresAt,
        ];

        // If refreshToken is not empty then encrypt it and add it to $updateData
        if (!empty($providerUser->refreshToken)) {
            $updateData['refresh_token'] = $this->tokenService->encrypt($providerUser->refreshToken);
        }

        $currentEmail = $this->save($user, $provider, $email, $updateData);

        $this->mailboxService->ensureDefaultMailboxes($currentEmail);

        return $currentEmail;
    }

    // Save it
    private function save(User $user, string $provider, string $email, array $updateData): EmailAccount{
       $emailAccount = EmailAccount::firstOrNew([
            'user_id' => $user->id,
            'provider' => $provider,
            'email_address' => $email,
        ]);

        $count = auth()->check()
            ? auth()->user()->emailAccounts()->count()
            : 0;

        $defaults = defaultAccountAppearance($provider, $count);

        $emailAccount->fill($updateData);

        if (blank($emailAccount->nickname)) {
            $emailAccount->nickname = $defaults['nickname'];
        }

        if (blank($emailAccount->color)) {
            $emailAccount->color = $defaults['color'];
        }

        if (blank($emailAccount->icon)) {
            $emailAccount->icon = $defaults['icon'];
        }

        $emailAccount->save();

        $currentEmail = $emailAccount;
        
        
        // If the user doesn't have a primary email account set...do so in preferences table
        if (!$user->userPreference || !$user->userPreference->primary_email_account) {
            UserPreference::updateOrCreate(
                ['user_id' => $user->id],
                ['primary_email_account' => $currentEmail->id]
            );
        }

        return $currentEmail;
    }
}