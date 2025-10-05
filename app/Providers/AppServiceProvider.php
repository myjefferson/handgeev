<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Configurar Cashier SEM automatic tax
        Cashier::calculateTaxes(false); // ← DESABILITA tax automático
        
        // Usar SUA model Subscription personalizada
        Cashier::useSubscriptionModel(\App\Models\Subscription::class);
        
        // Definir preços dos planos
        config(['services.stripe.prices' => [
            'start' => env('STRIPE_START_PRICE_ID'),
            'pro' => env('STRIPE_PRO_PRICE_ID'),
            'premium' => env('STRIPE_PREMIUM_PRICE_ID'),
        ]]);

        // Configurar rate limiting personalizado
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            // Este é o rate limiting padrão do Laravel
            // Nosso middleware personalizado vai lidar com os limites baseados no plano
            return Limit::none();
        });

        // Rate limiting para web (proteção contra abuso)
        RateLimiter::for('web', function (Request $request) {
            return $request->user()?->isPro() 
                ? Limit::none()
                : Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiting específico para criação de recursos
        RateLimiter::for('create-resources', function (Request $request) {
            $user = $request->user();
            
            if (!$user) {
                return Limit::perMinute(5)->by($request->ip());
            }

            // Limites mais generosos para usuários Pro
            if ($user->isPro()) {
                return Limit::perMinute(30)->by($user->id);
            }

            // Limites para Free
            if ($user->isFree()) {
                return Limit::perMinute(10)->by($user->id);
            }

            return Limit::perMinute(5)->by($user->id);
        });
    }
}