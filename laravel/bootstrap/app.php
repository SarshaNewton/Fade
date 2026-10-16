<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\EnsureLinkedAccount;
use App\Http\Middleware\EnsureCompleteSetup;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            EnsureCompleteSetup::class,
        ]);
    })
    ->withMiddleware(function ($middleware): void {
        $middleware->alias([
            'linked.account' => EnsureLinkedAccount::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
