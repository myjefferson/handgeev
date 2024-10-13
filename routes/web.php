<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PersonalDataController;
use App\Http\Controllers\PersonalProjectsController;
use App\Http\Controllers\ProfessionalExperiencesController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::controller(LoginController::class)->group(function(){
    Route::get('/login', 'index')->name('login.index');
    Route::get('/login/store', 'index')->name('login.store');
});

Route::controller(RegisterController::class)->group(function(){
    Route::get('/register', 'index')->name('register.index');
    // Route::get('/login', 'index')->name('login.store');
});

Route::controller(DashboardController::class)->group(function(){
    Route::get('/dashboard/home', 'index')->name('dashboard.home');
    Route::get('/dashboard/personal-data', '')->name('dashboard');
});

Route::controller(PersonalDataController::class)->group(function(){
    Route::get('/dashboard/personal-data', 'index')->name('dashboard.personal-data');
    Route::get('/dashboard/personal-data/edit/{id}', 'edit')->name('dashboard.personal-data.edit');
});

Route::controller(PersonalProjectsController::class)->group(function(){
    Route::get('/dashboard/personal-projects', 'index')->name('dashboard.personal-projects');
    Route::get('/dashboard/personal-projects/edit/{id}', 'edit')->name('dashboard.personal-projects.edit');
});

Route::controller(CoursesController::class)->group(function(){
    Route::get('/dashboard/courses', 'index')->name('dashboard.courses');
});

Route::controller(ProfessionalExperiencesController::class)->group(function(){
    Route::get('/dashboard/professional-experiences', 'index')->name('dashboard.professional-experiences');
});
