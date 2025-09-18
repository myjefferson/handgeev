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
use App\Http\Controllers\WorkspaceSettingController;
use App\Http\Controllers\WorkspaceCollaboratorController;
use App\Http\Controllers\NotificationController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function (){ return view('landing.portfoline'); } )->name('landing.portfoline');
Route::get('/offers', function (){ return view('landing.offers'); } )->name('landing.offers');
// Route::get('/plans/{plan}', [PlanController::class, 'show']);


Route::controller(LoginController::class)->group(function(){
    Route::get('/login', 'index')->name('login.index');
    Route::post('/login/auth', 'auth')->name('login.auth');
    Route::get('/logout', 'logout')->name('logout');
    Route::get('/account/inactive', function (){ return view('pages.auth.inactive-account'); })->name('account.inactive');
    Route::get('/account/suspended', function (){ return view('pages.auth.suspended-account'); })->name('account.suspended');
});
Route::get('/register', function (){ return view('pages.auth.register'); } )->name('register.index');

Route::controller(UserController::class)->group(function(){
    // Route::get('/register', 'index')->name('register.index');
    Route::post('/register/store', 'store')->name('register.store');
});


Route::middleware(['auth:web'])->group(function(){
    Route::controller(WorkspaceController::class)->group(function(){
        Route::get('/workspaces', 'listWorkspaces')->name('workspaces.myworkspaces');
        Route::get('/workspace/{id}', 'index')->name('workspace.show');
        Route::post('/workspace/store', 'store')->name('workspace.store');
        Route::put('/workspace/{id}/update', 'update')->name('workspace.update');
        Route::delete('/workspace/{id}/delete', 'destroy')->name('workspace.delete');
    });
    
    Route::controller(WorkspaceSettingController::class)->group(function(){
        Route::get('/workspace/setting/{id}', 'index')->name('workspace.setting');
        Route::put('/workspace/setting/hash/{id}/update', 'generateNewHashApi')->name('workspace.update.generateNewHashApi');
        // Route::post('/workspace/store', 'store')->name('workspace.store');
        // Route::put('/workspace/{id}/update', 'update')->name('workspace.update');
        // Route::delete('/workspace/{id}/delete', 'destroy')->name('workspace.delete');
    });

    Route::controller(WorkspaceCollaboratorController::class)->group(function () {
        Route::get('/workspace/collaborators/{workspaceId}', 'listCollaborators')->name('workspace.collaborators');
        Route::post('/workspace/collaborators/invite/{workspaceId}', 'inviteCollaborator')->name('workspace.collaborator.invite');
        Route::delete('/workspace/collaborators/{workspaceId}/{collaboratorId}', 'removeCollaborator')->name('workspace.collaborator.delete');
        Route::put('/workspace/collaborators/{collaborator}/role', 'updateCollaboratorRole')->name('workspace.collaborators.role.update');

        // Route::get('/accept/{token}', 'acceptInvite')->name('workspace.collaboration.accept');
        Route::post('/collaboration/accept/{token}', 'acceptInvite')->name('collaboration.invite.accept');
        // Recusar convite
        Route::post('/collaboration/reject/{token}', 'rejectInvite')->name('collaboration.invite.reject');
        // Rotas por ID (para ações diretas das notificações)
        Route::post('/{id}/accept', 'acceptInviteById')->name('workspace.invite.accept.id');
        Route::post('/{id}/reject', 'rejectInviteById')->name('workspace.invite.reject.id');
    });

    // Rota pública para aceitar convite
    // Route::get('/invite/accept/{token}', [WorkspaceAccessController::class, 'acceptInvite']);

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
        Route::get('/dashboard/about', 'about')->name('dashboard.about');
    });

    Route::controller(UserController::class)->group(function(){
        Route::get('/dashboard/profile', 'index')->name('user.profile');
        Route::get('/dashboard/profile/edit', 'edit')->name('user.profile.edit');
        Route::put('/dashboard/profile/update', 'update')->name('user.profile.update');
    });

    Route::controller(SettingController::class)->group(function(){
        Route::get('/dashboard/settings', 'index')->name('dashboard.settings');
        Route::put('/dashboard/settings/update/hash', 'generateNewHashApi')->name('dashboard.settings.update.hash');
        // Route::get('/dashboard/experiences/create', 'create')->name('dashboard.experiences.create');
    });

    Route::controller(NotificationController::class)->group(function(){
        Route::post('/{id}/read','markAsRead')->name('notifications.markAsRead');
        Route::post('/read-all','markAllAsRead')->name('notifications.markAllAsRead');
        Route::get('/count','count')->name('notifications.count');
    });

    // Rotas de Administração
    Route::middleware(['role:admin'])->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/admin/users', 'users')->name('admin.users');
            Route::put('/admin/users/{id}/update', 'updateUser')->name('admin.users.update');
            Route::delete('/admin/users/{id}/delete', 'deleteUser')->name('admin.users.delete');
            Route::put('/admin/plans', 'plans')->name('admin.plans');
        });
    });

    // Rotas para usuários pro (se necessário)
    // Route::middleware(['role:pro'])->group(function () {
    //     // Rotas específicas para plano pro
    // });
});

Route::get('/guimode', function (){ return view('pages.dashboard.shared-api.visual-mode'); } )->name('landing.offers');
Route::get('/user/notifications', function (Request $request) {
    return response()->json([
        'notifications' => $request->user()->notifications()->limit(10)->get(),
        'unread_count' => $request->user()->unreadNotifications()->count()
    ]);
});


//Modo GUI


//API
Route::post('/api/auth/refresh', [ApiController::class, 'refresh'])
->withoutMiddleware(ValidateCsrfToken::class)
->middleware('jwt.refresh');


Route::middleware(['authTokenApi'])->group(function(){
    Route::controller(ApiController::class)->group(function(){
            Route::post('/api/token/auth', 'getTokenByHashes')->name('api.token-auth');
            Route::get('/api/{id}/workspace', 'getWorkspaceData')->name('api.workspace');
    });
});