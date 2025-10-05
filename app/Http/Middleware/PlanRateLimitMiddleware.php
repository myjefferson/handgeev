<?php

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
        $plan = $user->getPlan(); // Usa o método atualizado que considera Stripe
        
        // Chave única para o rate limiting (user + workspace se aplicável)
        $workspaceId = $request->route('workspace_id') ?? $request->header('X-Workspace-ID');
        $rateLimitKey = $workspaceId ? 
            'api_requests:' . $user->id . ':' . $workspaceId : 
            'api_requests:' . $user->id;
        
        // Obter limites do plano considerando status do Stripe
        $limits = $this->getPlanLimits($plan, $user);
        
        // Verificar se usuário tem problemas de pagamento (limites reduzidos)
        if ($user->hasPaymentIssues()) {
            return $this->handlePaymentIssuesLimit($request, $next, $rateLimitKey, $user);
        }
        
        // Verificar limite burst (para picos de requisições)
        if (!$this->checkBurstLimit($rateLimitKey . ':burst', $limits['burst'])) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests too quickly. Please slow down.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':burst'),
                'limit_type' => 'burst'
            ], 429);
        }
        
        // Verificar limite por minuto
        if (!$this->checkRateLimit($rateLimitKey . ':minute', $limits['per_minute'], 60)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests. Please try again in a minute.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':minute'),
                'limit_type' => 'minute',
                'current_plan' => $plan->name
            ], 429);
        }
        
        // Verificar limite por hora
        if ($limits['per_hour'] > 0 && !$this->checkRateLimit($rateLimitKey . ':hour', $limits['per_hour'], 3600)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Hourly request limit exceeded. Please try again later.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':hour'),
                'limit_type' => 'hour',
                'current_plan' => $plan->name
            ], 429);
        }
        
        // Verificar limite por dia
        if ($limits['per_day'] > 0 && !$this->checkRateLimit($rateLimitKey . ':day', $limits['per_day'], 86400)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Daily request limit exceeded. Please try again tomorrow.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':day'),
                'limit_type' => 'daily',
                'current_plan' => $plan->name
            ], 429);
        }

        // Adicionar headers de rate limit na resposta
        $response = $next($request);
        
        return $this->addRateLimitHeaders($response, $rateLimitKey, $limits);
    }
    
    private function handlePublicRateLimit(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $rateLimitKey = 'public_api:' . $ip;
        
        // Limites mais restritivos para API pública
        if (!RateLimiter::attempt($rateLimitKey . ':minute', 30, function() {}, 60)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests from your IP. Please try again in a minute.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':minute')
            ], 429);
        }
        
        $response = $next($request);
        
        // Headers para API pública
        $response->headers->set('X-RateLimit-Limit', '30');
        $response->headers->set('X-RateLimit-Remaining', 
            RateLimiter::remaining($rateLimitKey . ':minute', 30));
        
        return $response;
    }
    
    private function handlePaymentIssuesLimit(Request $request, Closure $next, string $rateLimitKey, $user): Response
    {
        // Limites reduzidos para usuários com problemas de pagamento
        $reducedLimits = [
            'per_minute' => 10,
            'per_hour' => 100,
            'per_day' => 1000,
            'burst' => 2
        ];
        
        if (!$this->checkRateLimit($rateLimitKey . ':minute_payment', $reducedLimits['per_minute'], 60)) {
            return response()->json([
                'error' => 'Payment required',
                'message' => 'Your subscription has payment issues. Please update your payment method to restore full access.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey . ':minute_payment'),
                'billing_portal_url' => route('billing.portal')
            ], 429);
        }
        
        $response = $next($request);
        
        // Header indicando problemas de pagamento
        $response->headers->set('X-Payment-Status', 'issues');
        $response->headers->set('X-Billing-Portal', route('billing.portal'));
        
        return $response;
    }
    
    private function getPlanLimits($plan, $user): array
    {
        // Se usuário tem problemas de pagamento, retornar limites reduzidos
        if ($user->hasPaymentIssues()) {
            return [
                'per_minute' => 10,
                'per_hour' => 100,
                'per_day' => 1000,
                'burst' => 2
            ];
        }
        
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
    
    private function checkBurstLimit(string $key, int $maxAttempts): bool
    {
        if ($maxAttempts === 0) return true; // 0 = ilimitado
        
        return RateLimiter::attempt(
            $key, 
            $maxAttempts, 
            function() {},
            10 // 10 segundos para burst
        );
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
    
    private function addRateLimitHeaders(Response $response, string $rateLimitKey, array $limits): Response
    {
        $remainingMinute = RateLimiter::remaining($rateLimitKey . ':minute', $limits['per_minute']);
        $remainingHour = $limits['per_hour'] > 0 ? 
            RateLimiter::remaining($rateLimitKey . ':hour', $limits['per_hour']) : 'unlimited';
        $remainingDay = $limits['per_day'] > 0 ? 
            RateLimiter::remaining($rateLimitKey . ':day', $limits['per_day']) : 'unlimited';
        
        $response->headers->set('X-RateLimit-Limit-Minute', $limits['per_minute']);
        $response->headers->set('X-RateLimit-Remaining-Minute', $remainingMinute);
        $response->headers->set('X-RateLimit-Limit-Hour', $limits['per_hour']);
        $response->headers->set('X-RateLimit-Remaining-Hour', $remainingHour);
        $response->headers->set('X-RateLimit-Limit-Day', $limits['per_day']);
        $response->headers->set('X-RateLimit-Remaining-Day', $remainingDay);
        
        return $response;
    }
}