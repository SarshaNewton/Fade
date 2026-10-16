<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\EmailAccount;
use App\Models\SocialAccount;
use App\Services\Mail\MailboxService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Actions\HandleWorkspaceOnboarding;

class SocialAuthService{
    
    public function __construct(
        protected EmailAccountService $emailAccountService,
        protected SocialAccountService $socialAccountService,
        protected SocialAuthProviderManager $providerManager,
        protected HandleWorkspaceOnboarding $workspaceOnboarding,
        protected MailboxService $mailboxService
    ){}

    public function handleCallback(string $provider): RedirectResponse{
        $provider = strtolower($provider);
        $providerUser = $this->providerManager->for($provider)->user();
        $providerUserId = $providerUser->getId();
        $email = $this->extractEmailAddress($providerUser);

        // Case 1: User tried to login but the provider didn't provide an email
        if (!$email) {
            return redirect()->route('login')->withErrors('Provider did not return an email address.');
        }

        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->first();
        
        // Case 2: User is already logged in and is trying to connect a new login provider
        if (Auth::check()) {
            return $this->handleAuthenticatedUserLink( Auth::user(), $provider, $providerUser, $email);
        }

        // Case 3: User logged in with an existing social account
        if ($socialAccount) {
            return $this->handleExistingSocialAccount( $socialAccount, $provider, $providerUser, $email);
        }

        // Case 4: User logs in with an email address already tied to another account
        $existingUser = User::whereHas('emailAccounts', function ($query) use ($email) {
            $query->where('email_address', $email);
        })->first();
        // Pending variable is created for the email address the user is trying to connect
        if ($existingUser) {
            session([
                'pending_social_link' => [
                    'provider' => $provider,
                    'provider_user_id' => $providerUserId,
                    'provider_email' => $email,
                ],
            ]);
            // Then force to log in again with the login credentials tied to that email so that the account can be linked
            return redirect()->route('social.linkRequired');
        }

        //Case 5: User is completely new to Fade
        return $this->handleNewUser($provider, $providerUser, $email);
    }

    // If a user logs in and they have a social link pending 
    public function completePendingLink(?User $user): RedirectResponse {
        if (!$user) {
            return redirect()->route('login');
        }

        // Get the details from the session variable
        $pending = session('pending_social_link');

        // If for some reason it doesn't exist just bring the user to their inbox
        if (!$pending) {
            return redirect()->route('mail.index')->withErrors('No pending account to link.');
        }

        // Otherwise update their social accounts
        $this->socialAccountService->upsertPending($user, $pending);

        // Delete the variable
        session()->forget('pending_social_link');

        // Bring them to their inbox
        return redirect()->route('mail.index')->with('success', 'Account linked successfully.');
    }
    
    protected function handleExistingSocialAccount( SocialAccount $socialAccount, string $provider, $providerUser, string $email): RedirectResponse {
        $user = $socialAccount->user;

        Auth::login($user);
        $this->storeLastSocialLogin($email);

        $this->emailAccountService->syncFromProviderUser($user, $provider, $providerUser, $email);

        return redirect()->route('mail.index');
    }

    protected function handleAuthenticatedUserLink(User $user, string $provider, $providerUser, string $email): RedirectResponse {
        $existingSocialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $providerUser->getId())
            ->first();

        if ($existingSocialAccount && $existingSocialAccount->user_id !== $user->id) {
            return redirect()
                ->route($user->emailAccounts()->exists() ? 'accounts.index' : 'onboarding.show')
                ->withErrors([
                    'account' => 'This email account is already connected to another Fade account. Please sign into that Fade account first and disconnect it there before linking it here.'
                ]);
        }

        if ($this->emailAccountBelongsToAnotherUser($provider, $email, $user)) {
            return redirect()
                ->route($user->emailAccounts()->exists() ? 'accounts.index' : 'onboarding.show')
                ->withErrors([
                    'account' => 'This email account is already connected to another Fade account. Please use a different email account or sign into the existing Fade account.'
                ]);
        }

        $this->storeLastSocialLogin($email);

        $this->socialAccountService->upsert($user, $provider, $providerUser, $email);
        $emailAccount = $this->emailAccountService->syncFromProviderUser($user, $provider, $providerUser, $email);
        
        session(['selected_email_account_id' => $emailAccount->id]);

        if (! $user->setup_completed_at && $user->emailAccounts()->exists()) {
            $user->update([
                'setup_completed_at' => now(),
            ]);
        }

        return redirect()->route('mail.index');
    }

    protected function handleNewUser(string $provider, $providerUser, string $email): RedirectResponse {
        $user = User::create([
            'name' => $providerUser->getName() ?? '',
        ]);

        $this->socialAccountService->upsert($user, $provider, $providerUser, $email);
        $this->emailAccountService->syncFromProviderUser($user, $provider, $providerUser, $email);

        $this->workspaceOnboarding->handle($user, $email);

        Auth::login($user);
        $this->storeLastSocialLogin($email);

        if (is_null($user->setup_completed_at)) {
            return redirect()->route('onboarding.show');
        }

        return redirect()->route('mail.index');
    }
    
    protected function extractEmailAddress($providerUser): ?string {
        $email = $providerUser->getEmail();

        if (!$email && isset($providerUser->user)) {
            $email = $providerUser->user['mail']
                ?? $providerUser->user['userPrincipalName']
                ?? null;
        }

        return $email ? strtolower($email) : null;
    }

    protected function storeLastSocialLogin(string $email): void {
        session(['last_social_login' => $email]);
    }

    protected function emailAccountBelongsToAnotherUser(string $provider, string $email, User $user): bool {
        return EmailAccount::where('provider', $provider)
            ->where('email_address', $email)
            ->where('user_id', '!=', $user->id)
            ->exists();
    }
}