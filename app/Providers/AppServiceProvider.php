<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;
use Stripe\Stripe;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Configurar a API key do Stripe globalmente
        Stripe::setApiKey(config('services.stripe.secret'));
        
        if(env('APP_ENV') == 'production'){
            URL::forceScheme('https');
            if (! $this->app->environment('local')) {
            }
        }

        Cashier::calculateTaxes(false);
        Cashier::useSubscriptionModel(\App\Models\Subscription::class);
        
        // Configurar rate limiting personalizado
        $this->configureRateLimiting();

        // Compartilhar traduções com Inertia (para todas as páginas)
        Inertia::share([
            'locale' => fn() => App::getLocale(),
            'available_locales' => config('app.available_locales', [
                'pt_BR' => 'Português',
                'en' => 'English',
                'es' => 'Español'
            ]),
            'translations' => function () {
                $locale = App::getLocale();
                $langPath = lang_path($locale);
                $translations = [];

                if (File::isDirectory($langPath)) {
                    foreach (File::allFiles($langPath) as $file) {
                        $key = pathinfo($file, PATHINFO_FILENAME);
                        $translations[$key] = include $file->getPathname();
                    }
                }

                return $translations;
            },
        ]);
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