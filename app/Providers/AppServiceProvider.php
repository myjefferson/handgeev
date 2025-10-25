<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Configurar a API key do Stripe globalmente
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        
        if(env('APP_ENV') == 'production'){
            if (! $this->app->environment('local')) {
                URL::forceScheme('https');
            }
        }

        Cashier::calculateTaxes(false);
        Cashier::useSubscriptionModel(\App\Models\Subscription::class);
        
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

            // Admin - sem limites (ou limites muito altos)
            if ($user->isAdmin()) {
                return Limit::none();
            }

            // Limites mais generosos para usuários Premium
            if ($user->isPremium()) {
                return Limit::perMinute(250)->by($user->id);
            }
            
            // Limites para usuários Pro
            if ($user->isPro()) {
                return Limit::perMinute(120)->by($user->id);
            }
            
            // Limites para usuários Start
            if ($user->isStart()) {
                return Limit::perMinute(60)->by($user->id);
            }

            // Limites para Free (padrão)
            return Limit::perMinute(30)->by($user->id);
        });
    }
}