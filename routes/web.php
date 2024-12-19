<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PersonalDataController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ExperiencesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SettingsController;
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
    Route::controller(DashboardController::class)->group(function(){
        Route::get('/dashboard/home', 'index')->name('dashboard.home');
        Route::get('/dashboard/personal-data', '')->name('dashboard');
    });

    Route::controller(PersonalDataController::class)->group(function(){
        Route::get('/dashboard/personal-data', 'index')->name('dashboard.personal-data');
        Route::get('/dashboard/personal-data/edit', 'edit')->name('dashboard.personal-data.edit');
        Route::put('/dashboard/personal-data/update', 'update')->name('dashboard.personal-data.update');
    });

    Route::controller(ProjectsController::class)->group(function(){
        Route::get('/dashboard/projects', 'index')->name('dashboard.projects');
        Route::get('/dashboard/projects/create', 'create')->name('dashboard.projects.create');
        Route::post('/dashboard/projects/store', 'store')->name('dashboard.projects.store');
        Route::get('/dashboard/projects/edit', 'edit')->name('dashboard.projects.edit');
    });

    Route::controller(CoursesController::class)->group(function(){
        Route::get('/dashboard/courses', 'index')->name('dashboard.courses');
        Route::get('/dashboard/courses/create', 'create')->name('dashboard.courses.create');
        Route::post('/dashboard/courses/store', 'store')->name('dashboard.courses.store');
    });

    Route::controller(ExperiencesController::class)->group(function(){
        Route::get('/dashboard/experiences', 'index')->name('dashboard.experiences');
        Route::get('/dashboard/experiences/create', 'create')->name('dashboard.experiences.create');
        Route::post('/dashboard/experiences/store', 'store')->name('dashboard.experiences.store');
    });

    Route::controller(SettingsController::class)->group(function(){
        Route::get('/dashboard/settings', 'index')->name('dashboard.settings');
        Route::post('/dashboard/settings/generate/newHashApi', 'generateNewHashApi')->name('dashboard.settings.generateNewHashApi');
        // Route::get('/dashboard/experiences/create', 'create')->name('dashboard.experiences.create');
    });
});

//Api
Route::middleware(['authTokenApi'])->group(function(){
    Route::controller(ApiController::class)
        ->withoutMiddleware(ValidateCsrfToken::class)
        ->group(function(){
        Route::any('/api/profile', 'getPersonalData')->name('api.personal-data');
        Route::any('/api/experiences', 'getExperiences')->name('api.experiences');
        Route::any('/api/courses', 'getCourses')->name('api.courses');
        Route::any('/api/projects', 'getProjects')->name('api.projects');
        // Route::get('/api/experiences/{userId}', 'getPersonalProjects')->name('api.projects');
    });
});

