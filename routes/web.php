<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TopicController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;


Route::controller(LoginController::class)->group(function(){
    Route::get('/login', 'index')->name('login.index');
    Route::post('/login/auth', 'auth')->name('login.auth');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function(){
    Route::get('/register', 'index')->name('register.index');
    Route::post('/register/store', 'store')->name('register.store');
});

Route::middleware(['auth'])->group(function(){
    Route::controller(WorkspaceController::class)->group(function(){
        Route::get('/workspace/{id}', 'index')->name('workspace.index');
        Route::post('/workspace/store', 'store')->name('workspace.store');
    });

    Route::controller(FieldController::class)->group(function(){
        Route::post('/field/store', 'store')->name('field.store');
        Route::put('/field/{id}/update', 'update')->name('field.update');
        Route::delete('/field/destroy', 'destroy')->name('field.destroy');
    });

    Route::controller(TopicController::class)->group(function(){
        Route::post('/topic/store', 'store')->name('topic.store');
        Route::put('/topic/{id}/update', 'update')->name('topic.update');
        Route::delete('/topic/destroy', 'destroy')->name('topic.destroy');
    });

    Route::controller(DashboardController::class)->group(function(){
        Route::get('/dashboard/home', 'index')->name('dashboard.home');
        Route::get('/dashboard/personal-data', '')->name('dashboard');
        Route::get('/dashboard/about', 'about')->name('dashboard.about');
    });

    Route::controller(SettingController::class)->group(function(){
        Route::get('/dashboard/settings', 'index')->name('dashboard.settings');
        Route::post('/dashboard/settings/generate/newHashApi', 'generateNewHashApi')->name('dashboard.settings.generateNewHashApi');
        // Route::get('/dashboard/experiences/create', 'create')->name('dashboard.experiences.create');
    });
});


//API
Route::post('/api/auth/refresh', [ApiController::class, 'refresh'])
->withoutMiddleware(ValidateCsrfToken::class)
->middleware('jwt.refresh');

Route::controller(ApiController::class)
    ->withoutMiddleware(ValidateCsrfToken::class)
    ->group(function(){
        Route::post('/api/token/auth', 'getTokenByHashes')->name('api.token-auth');
});
Route::middleware(['authTokenApi'])->group(function(){
    Route::controller(ApiController::class)
        ->withoutMiddleware(ValidateCsrfToken::class)
        ->group(function(){
        Route::get('/api/profile', 'getPersonalData')->name('api.personal-data');
        Route::get('/api/experiences', 'getExperiences')->name('api.experiences');
        Route::get('/api/courses', 'getCourses')->name('api.courses');
        Route::get('/api/coursebyid', 'getCourseById')->name('api.courseById');
        Route::get('/api/projects', 'getProjects')->name('api.projects');
        Route::get('/api/projectbyid', 'getProjectById')->name('api.projectById');
        // Route::get('/api/experiences/{userId}', 'getPersonalProjects')->name('api.projects');
    });
});