<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Inicializa quaisquer serviços da aplicação.
     */
    public function boot(): void
    {
        Blade::if('free', function () {
            // Chama o método isFree() do modelo User
            return auth()->user() && auth()->user()->isFree();
        });

        Blade::if('pro', function () {
            // Chama o método isPro() do modelo User
            return auth()->user() && auth()->user()->isPro();
        });

        Blade::if('start', function () {
            // Chama o método isPro() do modelo User
            return auth()->user() && auth()->user()->isStart();
        });
        
        Blade::if('premium', function () {
            // Chama o método isPro() do modelo User
            return auth()->user() && auth()->user()->isPremium();
        });

        Blade::if('admin', function () {
            // Chama o método isAdmin() do modelo User
            return auth()->user() && auth()->user()->isAdmin();
        });
    }
}