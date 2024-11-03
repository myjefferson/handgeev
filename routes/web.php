<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PersonalDataController;
use App\Http\Controllers\PersonalProjectsController;
use App\Http\Controllers\ExperiencesController;
use App\Http\Controllers\RegisterController;
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

    Route::controller(PersonalProjectsController::class)->group(function(){
        Route::get('/dashboard/personal-projects', 'index')->name('dashboard.personal-projects');
        Route::get('/dashboard/personal-projects/edit', 'edit')->name('dashboard.personal-projects.edit');
    });

    Route::controller(CoursesController::class)->group(function(){
        Route::get('/dashboard/courses', 'index')->name('dashboard.courses');
        Route::get('/dashboard/courses/create', 'create')->name('dashboard.courses.create');
    });

    Route::controller(ExperiencesController::class)->group(function(){
        Route::get('/dashboard/experiences', 'index')->name('dashboard.experiences');
        Route::get('/dashboard/experiences/create', 'create')->name('dashboard.experiences.create');
        Route::post('/dashboard/experiences/store', 'store')->name('dashboard.experiences.store');
    });
});

//Api
Route::controller(ApiController::class)->group(function(){
    Route::get('/api/personal-data/{userId}', 'getPersonalData')->name('api.personal-data');
    Route::get('/api/experiences/{userId}', 'getExperiences')->name('api.experiences');
});


