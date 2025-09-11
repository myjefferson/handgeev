<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\TopicController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function (){ return view('landing.portfoline'); } )->name('landing.portfoline');
Route::get('/offers', function (){ return view('landing.offers'); } )->name('landing.offers');
// Route::get('/plans/{plan}', [PlanController::class, 'show']);


Route::controller(LoginController::class)->group(function(){
    Route::get('/login', 'index')->name('login.index');
    Route::post('/login/auth', 'auth')->name('login.auth');
    Route::get('/logout', 'logout')->name('logout');
});

Route::get('/register', function (){ return view('pages.auth.register'); } )->name('register.index');
Route::controller(UserController::class)->group(function(){
    // Route::get('/register', 'index')->name('register.index');
    Route::post('/register/store', 'store')->name('register.store');
});


Route::middleware(['auth:web'])->group(function(){
    Route::controller(WorkspaceController::class)->group(function(){
        Route::get('/workspace/{id}', 'index')->name('workspace.index');
        Route::post('/workspace/store', 'store')->name('workspace.store');
        Route::put('/workspace/{id}/update', 'update')->name('workspace.update');
        Route::delete('/workspace/{id}/delete', 'destroy')->name('workspace.delete');
    });

    Route::controller(FieldController::class)->group(function(){
        Route::post('/field/store', 'store')->name('field.store');
        Route::put('/field/{id}/update', 'update')->name('field.update');
        Route::delete('/field/{id}/destroy', 'destroy')->name('field.destroy');
        Route::post('/field/check-limit', 'checkLimit')->name('fields.checkLimit');
    });

    Route::controller(TopicController::class)->group(function(){
        Route::post('/topic/store', 'store')->name('topic.store');
        Route::put('/topic/{id}/update', 'update')->name('topic.update');
        Route::delete('/topic/{id}/destroy', 'destroy')->name('topic.destroy');
    });

    Route::controller(DashboardController::class)->group(function(){
        Route::get('/dashboard/home', 'index')->name('dashboard.home');
        Route::get('/dashboard/profile', '')->name('dashboard');
        Route::get('/dashboard/about', 'about')->name('dashboard.about');
    });

    Route::controller(SettingController::class)->group(function(){
        Route::get('/dashboard/settings', 'index')->name('dashboard.settings');
        Route::post('/dashboard/settings/generate/newHashApi', 'generateNewHashApi')->name('dashboard.settings.generateNewHashApi');
        // Route::get('/dashboard/experiences/create', 'create')->name('dashboard.experiences.create');
    });

    // Rotas de Administração
    Route::middleware(['role:admin'])->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/administration/users', 'users')->name('admin.users');
            Route::get('/administration/plans', 'plans')->name('admin.plans');
        });
    });

    // Rotas para usuários pro (se necessário)
    // Route::middleware(['role:pro'])->group(function () {
    //     // Rotas específicas para plano pro
    // });
});


//API
Route::post('/api/auth/refresh', [ApiController::class, 'refresh'])
->withoutMiddleware(ValidateCsrfToken::class)
->middleware('jwt.refresh');


Route::middleware(['auth:api'])->group(function(){
    Route::controller(ApiController::class)->group(function(){
            Route::post('/api/token/auth', 'getTokenByHashes')->name('api.token-auth');
    });
    Route::middleware(['authTokenApi'])->group(function(){
        Route::controller(ApiController::class)->group(function(){
            Route::get('/api/{id}/workspace', 'getWorkspaceData')->name('api.workspace');
        });
    });
});