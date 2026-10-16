<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComposeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\EmailBulkActionController;
use App\Http\Controllers\Auth\SocialAuthController;

use App\Models\Email;
use App\Services\Ai\AiSanitizerService;
use App\Services\Security\EmailSecurityScanner;
use App\Services\Security\Checks\HeaderRiskCheck;
use App\Services\Security\DTO\SecurityScanResult;

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'linked.account'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* Login Stuff */
Route::get('/onboarding', \App\Livewire\OnboardingWizard::class)
    ->name('onboarding.show');

// Social Authentication routes
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback');

// If provider email matches an existing account but isn't linked yet
Route::get('/auth/link-required', [SocialAuthController::class, 'linkRequired'])
    ->name('social.linkRequired');
Route::post('/auth/link-complete', [SocialAuthController::class, 'completePendingLink'])
    ->name('social.link.complete');

// After user logs in normally, complete the pending link
Route::post('/auth/link-complete', [SocialAuthController::class, 'linkComplete'])
    ->middleware('auth')
    ->name('social.linkComplete');


/* Mail Stuff */
//All
Route::middleware(['auth'])->group(function () {
    Route::get('/index', [EmailBulkActionController::class, 'index'])
        ->middleware('linked.account')
        ->name('mail.index');
    Route::post('/emails/bulk-action', [EmailBulkActionController::class, 'bulkAction'])
        ->middleware('linked.account')
        ->name('emails.bulk-action');
    Route::post('/emails/{email}/trash', [EmailBulkActionController::class, 'trash'])
        ->middleware('linked.account')
        ->name('emails.trash');
    Route::post('/emails/{email}/star', [EmailBulkActionController::class, 'toggleStar'])
        ->middleware('linked.account')
        ->name('emails.star');

    Route::get('/accounts/index', [EmailAccountController::class, 'index'])
        ->name('accounts.index');
    Route::post('/accounts/{account}/switch', [EmailAccountController::class, 'switch'])
        ->middleware('linked.account')
        ->name('accounts.switch');
    Route::delete('/accounts/{account}/disconnect', [EmailAccountController::class, 'disconnect'])
        ->middleware('linked.account')
        ->name('accounts.disconnect');
});

//Inbox
Route::middleware(['auth', 'linked.account'])->group(function () {
    Route::post('/inbox/{account}/sync', [InboxController::class, 'sync'])
        ->name('mail.sync');
});

//Compose
Route::middleware(['auth', 'linked.account'])->group(function () {
    Route::get('/compose/{account}', [ComposeController::class, 'open'])
        ->name('compose.create');
    Route::get('/compose/{account}/draft/{draft}', [ComposeController::class, 'open'])
        ->name('compose.open');
    Route::get('/emails/{email}/reply', [ComposeController::class, 'reply'])
        ->name('emails.reply');
    Route::get('/emails/{email}/replyall', [ComposeController::class, 'replyAll'])
        ->name('emails.replyAll');
    Route::get('/emails/{email}/forward', [ComposeController::class, 'forward'])
        ->name('emails.forward');
    Route::post('/compose/{account}/save', [ComposeController::class, 'save'])
        ->name('compose.save');
    Route::post('/compose/{account}/send', [ComposeController::class, 'send'])
        ->name('compose.send');
});

//Sent
Route::get('/mail/{account}/sent', [SentController::class, 'sent'])
    ->middleware('auth', 'linked.account')
    ->name('mail.sent');

//Analyze
Route::post('/emails/{email}/analyze', [AiController::class, 'analyze'])
    ->middleware('auth', 'linked.account');


/* Blade Stuff*/
Route::post('/fade-mode/toggle', function () {
    $newMode = ! session('fade_mode', false);

    session(['fade_mode' => $newMode]);

    return response()->json([
        'fade_mode' => $newMode,
    ]);
})->name('fade-mode.toggle')->middleware('auth');



require __DIR__.'/auth.php';
