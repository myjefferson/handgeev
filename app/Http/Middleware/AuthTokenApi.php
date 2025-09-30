<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Support\Facades\RateLimiter;

class AuthTokenApi
{
    public function handle(Request $request, Closure $next): Response
    {
        // Aplicar rate limiting antes da autenticação para proteger contra ataques
        if (!$this->checkPublicRateLimit($request)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests. Please try again in a minute.'
            ], 429);
        }

        try {            
            if(!Auth::check() && !Auth::user()){
                $user = JWTAuth::parseToken()->authenticate();

                if (!$user && !Auth::check()) {
                    return response()->json(['error' => 'Usuário não encontrado.'], 404);
                }
            }

        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expirado.'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['error' => 'Token na lista negra.'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Token de autenticação não fornecido ou inválido.'], 401);
        }

        // Após autenticação, aplicar rate limiting baseado no plano do usuário
        if (Auth::check()) {
            if (!$this->checkUserRateLimit($request)) {
                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Request limit exceeded for your plan. Please try again later.'
                ], 429);
            }
        }

        return $next($request);
    }

    /**
     * Rate limiting para requisições não autenticadas (por IP)
     */
    private function checkPublicRateLimit(Request $request): bool
    {
        $key = 'public_api:' . $request->ip();
        
        return RateLimiter::attempt(
            $key . ':minute', 
            30, // 30 requisições por minuto por IP
            function() {},
            60
        );
    }

    /**
     * Rate limiting baseado no plano do usuário
     */
    private function checkUserRateLimit(Request $request): bool
    {
        $user = Auth::user();
        $plan = $user->getPlan();
        
        if (!$plan || !$plan->can_use_api) {
            return false;
        }

        $limits = $this->getPlanLimits($plan);
        $userKey = 'user_api:' . $user->id;

        // Verificar limite por minuto
        if (!$this->checkRateLimit($userKey . ':minute', $limits['per_minute'], 60)) {
            return false;
        }
        
        // Verificar limite por hora
        if ($limits['per_hour'] > 0 && !$this->checkRateLimit($userKey . ':hour', $limits['per_hour'], 3600)) {
            return false;
        }
        
        // Verificar limite por dia
        if ($limits['per_day'] > 0 && !$this->checkRateLimit($userKey . ':day', $limits['per_day'], 86400)) {
            return false;
        }

        return true;
    }

    private function getPlanLimits($plan): array
    {
        if (!$plan) {
            return [
                'per_minute' => 60,
                'per_hour' => 1000,
                'per_day' => 10000,
            ];
        }
        
        return [
            'per_minute' => $plan->api_requests_per_minute ?? 60,
            'per_hour' => $plan->api_requests_per_hour ?? 1000,
            'per_day' => $plan->api_requests_per_day ?? 10000,
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