<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\TypeWorkspace;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // VIEW COMPOSERS
        View::composer('dashboard', function ($view) {
            $data = [
                'workspaces' => auth()->check() ? auth()->user()->workspaces : [],
                'typeWorkspaces' => TypeWorkspace::all(),
                'appVersion' => env('APP_VERSION')
            ];
            $view->with($data);
        });

        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('workspaces', auth()->user()->workspaces);
            }
        });
    }
}