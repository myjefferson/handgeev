<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AuthTokenApi;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\PlanRateLimitMiddleware;
use App\Http\Middleware\CheckApiAccessMiddleware;
use App\Http\Middleware\CheckPlanLimits;
use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\CheckSubscription;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->redirectGuestsTo(fn (Request $request) => route('login.show'));
        $middleware->alias([
            'auth' => Authenticate::class,
            'role' => RoleMiddleware::class,
            'plan.rate_limit' => CheckPlanLimits::class,
            'api.auth_token' => AuthTokenApi::class,
            'api.access' => CheckApiAccessMiddleware::class,
            'api.log' => LogApiRequests::class,
            'api.log' => LogApiRequests::class,
            'languages' => LanguageMiddleware::class,
            'subscribed' => CheckSubscription::class,
            'plan.limits' => CheckPlanLimits::class,
            'plan.rate_limit' => PlanRateLimitMiddleware::class,
            // 'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
