<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;
use App\Models\TypeWorkspace;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartilhar dados com o template específico
        View::composer('template', function ($view) {
            $data = [
                'workspaces' => auth()->check() ? auth()->user()->workspaces : [],
                'typeWorkspaces' => TypeWorkspace::all(),
                'appVersion' => env('APP_VERSION', '1.0.0')
            ];

            $view->with($data);
        });

        // Ou compartilhar com todas as views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('workspaces', auth()->user()->workspaces);
            }
        });
    }
}
