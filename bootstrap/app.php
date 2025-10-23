<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AuthTokenApi;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\PlanRateLimitMiddleware;
use App\Http\Middleware\CheckApiAccessMiddleware;
use App\Http\Middleware\CheckPlanLimits;
use App\Http\Middleware\CheckApiEnabled;
use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\RecordLastLogin;
use App\Http\Middleware\CheckSuspendedUser;

use App\Http\Middleware\CheckDeactivatedAccount;

use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\CheckAllowedDomain;
use App\Http\Middleware\WorkspacePasswordMiddleware;
use App\Http\Middleware\CheckApiMethodPermission;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->redirectGuestsTo(fn (Request $request) => route('login.show'));
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        
        // Ou configure CORS com opções específicas
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'auth/token',
            'health'
        ]);
        $middleware->alias([
            'auth' => Authenticate::class,
            'role' => RoleMiddleware::class,
            'plan.rate_limit' => CheckPlanLimits::class,
            'api.auth_token' => AuthTokenApi::class,
            'languages' => LanguageMiddleware::class,
            'subscribed' => CheckSubscription::class,
            'plan.limits' => CheckPlanLimits::class,
            'plan.rate_limit' => PlanRateLimitMiddleware::class,
            'workspace.api.password' => WorkspacePasswordMiddleware::class,
            'check.api.access' => CheckApiAccessMiddleware::class,
            'check.api.method' => CheckApiMethodPermission::class,
            'check.api.enabled' => CheckApiEnabled::class,
            'check.api.domain' => CheckAllowedDomain::class,
            'check.user.suspended' => CheckSuspendedUser::class,
            'log.api.request' => LogApiRequest::class,
            'record.last.login' => RecordLastLogin::class,
            'account.deactivated' => CheckDeactivatedAccount::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
