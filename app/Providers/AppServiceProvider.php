<?php

namespace App\Providers;

use View;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\TypeWorkspace;
use App\Models\Workspace;

class AppServiceProvider extends ServiceProvider
{

    // protected $policies = [
    //     Workspace::class => WorkspacePolicy::class,
    // ];

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
        $this->registerPolicies();

        // Gates básicas baseadas no plano
        Gate::define('export-data', function ($user) {
            return $user->canExportData();
        });

        Gate::define('use-api', function ($user) {
            return $user->canUseApi();
        });

        Gate::define('create-workspace', function ($user) {
            return $user->canCreateWorkspace();
        });

        // Gate para admin apenas
        Gate::define('view-admin', function ($user) {
            return $user->isAdmin();
        });
        
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
