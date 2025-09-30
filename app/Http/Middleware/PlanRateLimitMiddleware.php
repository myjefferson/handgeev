<?php
// app/Http/Middleware/PlanRateLimitMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class PlanRateLimitMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se não há usuário autenticado (API pública), usar limite por IP
        if (!auth()->check()) {
            return $this->handlePublicRateLimit($request, $next);
        }

        $user = auth()->user();
        $plan = $user->getPlan(); // Supondo que você tenha esse método
        
        // Chave única para o rate limiting (user + workspace se aplicável)
        $rateLimitKey = 'api_requests:' . $user->id;
        
        // Obter limites do plano
        $limits = $this->getPlanLimits($plan);
        
        // Verificar limite por minuto
        if (!$this->checkRateLimit($rateLimitKey . ':minute', $limits['per_minute'], 60)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests. Please try again in a minute.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':minute')
            ], 429);
        }
        
        // Verificar limite por hora
        if ($limits['per_hour'] > 0 && !$this->checkRateLimit($rateLimitKey . ':hour', $limits['per_hour'], 3600)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Hourly request limit exceeded. Please try again later.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':hour')
            ], 429);
        }
        
        // Verificar limite por dia
        if ($limits['per_day'] > 0 && !$this->checkRateLimit($rateLimitKey . ':day', $limits['per_day'], 86400)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Daily request limit exceeded. Please try again tomorrow.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':day')
            ], 429);
        }

        return $next($request);
    }
    
    private function handlePublicRateLimit(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $rateLimitKey = 'public_api:' . $ip;
        
        // Limites mais restritivos para API pública
        if (!RateLimiter::attempt($rateLimitKey . ':minute', 30, function() {}, 60)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests from your IP. Please try again in a minute.'
            ], 429);
        }
        
        return $next($request);
    }
    
    private function getPlanLimits($plan): array
    {
        // Limites padrão (free) se plano não for encontrado
        if (!$plan) {
            return [
                'per_minute' => 60,
                'per_hour' => 1000,
                'per_day' => 10000,
                'burst' => 10
            ];
        }
        
        return [
            'per_minute' => $plan->api_requests_per_minute ?? 60,
            'per_hour' => $plan->api_requests_per_hour ?? 1000,
            'per_day' => $plan->api_requests_per_day ?? 10000,
            'burst' => $plan->burst_requests ?? 10
        ];
    }
    
    private function checkRateLimit(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        if ($maxAttempts === 0) return true; // 0 = ilimitado
        
        return RateLimiter::attempt(
            $key, 
            $maxAttempts, 
            function() {},
            $decaySeconds
        );
    }
}