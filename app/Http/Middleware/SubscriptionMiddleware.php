<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Admin não precisa de verificação de assinatura
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verificar se a assinatura está ativa
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'error' => 'Your subscription is not active',
                'requires_upgrade' => true
            ], 403);
        }

        return $next($request);
    }
}
