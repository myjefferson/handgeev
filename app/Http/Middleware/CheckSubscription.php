<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next, $plan = 'free')
    {
        $user = Auth::user();
        
        if (in_array($plan, ['pro', 'admin']) && !$user->subscribed('default')) {
            return redirect()->route('subscription.pricing')
                ->with('error', 'Você precisa assinar o plano Pro para acessar este recurso.');
        }

        return $next($request);
    }
}