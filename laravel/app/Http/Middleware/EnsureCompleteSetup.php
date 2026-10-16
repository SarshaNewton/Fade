<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

//Ensures that setup is complete by disallowing users to go to any requested routes unless setup is complete
class EnsureCompleteSetup
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
        ];

        $currentName = optional($request->route())->getName();

        //
        if(in_array($currentName, $allowedRouteNames, true)){
            return $next($request);
        }

        //If user didn't complete onboarding then force them to do so
        if(is_null($user->setup_completed_at)){
            return redirect()->route('onboarding.show');
        }

         return $next($request);
    }
}
