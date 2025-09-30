<?php
// app/Http/Middleware/CheckApiAccessMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Se é uma requisição pública, permitir (já que são apenas GETs)
        if (!$user) {
            return $next($request);
        }
        
        $plan = $user->getPlan();
        
        // Verificar se o plano permite acesso à API
        if (!$plan || !$plan->can_use_api) {
            return response()->json([
                'error' => 'API access denied',
                'message' => 'Your current plan does not include API access. Please upgrade your plan.'
            ], 403);
        }
        
        return $next($request);
    }
}