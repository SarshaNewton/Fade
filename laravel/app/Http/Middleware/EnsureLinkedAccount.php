<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

//Ensures that an account has a primary email
class EnsureLinkedAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        //Livewire makes requests so it is allowed to do whatever it wants
        if ($request->is('livewire*')) {
            return $next($request);
        }

        //If user is not logged in allow the request
        if(!Auth::check()){
            return $next($request);
        }

        $user = Auth::user();

        //Only allowed routes to stop infinite loops and allow OAuth to complete
        $allowedRouteNames = [
            'onboarding.show', 
            'logout',
            'social.redirect',
            'social.callback',
            'social.linkRequired',
            'social.link.complete',
            'accounts.index',
        ];

        $currentName = optional($request->route())->getName();

        //
        if(in_array($currentName, $allowedRouteNames, true)){
            return $next($request);
        }

        //If user doesnt have a primary email account
        $primaryAccountId = $user->userPreference?->primary_email_account;

        if (!$primaryAccountId) {
            return redirect()->route('accounts.index');
        }

         return $next($request);
    }
}
