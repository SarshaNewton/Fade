<?php

namespace App\Providers;

use App\Services\Ai\Providers\FakeAiProvider;
use App\Services\Ai\Contracts\AiProviderInterface;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AiProviderInterface::class, function () {
            return match (config('ai.provider')) {
                'gemini' => app(\App\Services\Ai\Providers\GeminiAiProvider::class),
                default => app(\App\Services\Ai\Providers\FakeAiProvider::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('microsoft', \SocialiteProviders\Microsoft\Provider::class);
        });
    }
}
