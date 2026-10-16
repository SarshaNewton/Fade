<?php

namespace App\Http\Controllers\Auth;

use App\Services\Auth\SocialAuthService;
use App\Services\Auth\SocialAuthProviderManager;

use App\Http\Controllers\Controller;

class SocialAuthController extends Controller {
    public function __construct(protected SocialAuthProviderManager $providerManager, protected SocialAuthService $socialAuthService) {}

    // Redirect to the provider's login
    public function redirect(string $provider){
        return $this->providerManager->for($provider)->redirect();
    }

    // Get the deets from the provider and handle callback details
    public function callback(string $provider){
        return $this->socialAuthService->handleCallback($provider);
    }

    // If the user logged in with an email already tied to another, store the details in a session variable and force them to login with the deets tied to the email
    public function linkRequired(){
        return view('auth.link-required');
    }

    // Finish the pending link if there is a link pending
    public function completePendingLink(){
        return $this->socialAuthService->completePendingLink(auth()->user());
    }
}