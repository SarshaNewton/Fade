<?php

namespace App\Services\Auth\Contracts;

interface SocialAuthProviderInterface {
    public function supports(string $provider): bool;
    public function redirect();
    public function user();
}