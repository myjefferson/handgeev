<?php

namespace App\Providers;

use View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\WorkspacePolicy;
use App\Models\TypeWorkspace;
use App\Models\Workspace;
use App\Models\Role;
use Auth;

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
        $this->registerPolicies();

        // ========== GATES ==========
        Gate::policy(Workspace::class, WorkspacePolicy::class);

        Gate::define('export-data', function ($user) {
            return $user->canExportData();
        });

        Gate::define('use-api', function ($user) {
            return $user->canUseApi();
        });

        Gate::define('create-workspace', function ($user) {
            return $user->canCreateWorkspace();
        });

        Gate::define('view-admin', function ($user) {
            return $user->isAdmin();
        });
    }
}