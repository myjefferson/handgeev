<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\WorkspaceSettingController;
use App\Http\Controllers\WorkspaceSharedController;
use App\Http\Controllers\WorkspaceDomainController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LanguageController;

use App\Http\Controllers\StripeWebhookController;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::middleware(['languages'])->group(function(){
    Route::get('/', function (){ return view('landing.portfoline'); } )->name('landing.portfoline');
    Route::get('/offers', function (){ return view('subscription.pricing'); } )->name('subscription.pricing');
    
    // Termos e Privacidade
    Route::get('/terms', function () { return view('legal.terms'); })->name('legal.terms');
    Route::get('/privacy', function () { return view('legal.privacy'); })->name('legal.privacy');
    
    // Rotas para visualização compartilhada
    Route::controller(WorkspaceSharedController::class)->group(function(){
        Route::get('/api/interface/{global_hash_api?}/{workspace_hash_api?}', 'showInterfaceApi')->name('workspace.shared.interface.api');
    });
    
    Route::get('/teste', function (){ return view('pages.dashboard.teste'); })->name('teste.page');
    
    Route::controller(UserController::class)->group(function(){
        Route::post('/register/store', 'storeProfile')->name('register.store');
    });
    
    Route::get('/account/inactive', function (){ return view('pages.auth.inactive-account'); })->name('account.inactive');
    Route::get('/account/suspended', function (){ return view('pages.auth.suspended-account'); })->name('account.suspended');
    
    Route::controller(AccountController::class)->group(function(){
        Route::get('/login', 'indexLogin')->name('login.show');
        Route::post('/login/auth', 'authLogin')->name('login.auth');
        Route::get('/logout', 'logout')->name('logout');
        Route::get('/account/register', 'indexRegister')->name('register.index');
        Route::get('/support/recovery-account', 'showRecovery')->name('recovery.account.show');
        Route::get('/support/recovery-password/{token}','showResetForm')->name('recovery.password.show');
        Route::post('/support/recovery-password','updatePasswordRecovery')->name('recovery.password.update');
    });
    
    Route::controller(EmailController::class)->group(function(){
        Route::get('/verificar-email', 'showVerifyEmail')->name('verification.show');
        Route::post('/recovery-password/email', 'sendRecoveryEmail')->name('recovery.password.email');
        Route::post('/verificar-email', 'verifyEmail')->name('verification.verify');
        Route::post('/reenviar-codigo', 'resendVerifyEmail')->name('verification.resend');
        Route::post('/alterar-email', 'updateVerifyEmail')->name('verification.update-email');
    });

    // Webhook Stripe (sem CSRF protection)
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
        ->withoutMiddleware([ValidateCsrfToken::class]);
});


Route::middleware(['auth:web', 'languages', 'plan.limits'])->group(function(){
    
    // Alertas de limites serão mostrados automaticamente pelo middleware plan.limits
    
    Route::controller(WorkspaceSharedController::class)->group(function(){
        Route::get('/api/rest/workspace/{id}', 'showApiRest')->name('workspace.api-rest');
    });

    Route::controller(WorkspaceController::class)->group(function(){
        Route::get('/workspaces', 'indexWorkspaces')->name('workspaces.index');
        Route::get('/workspace/{id}', 'index')->name('workspace.show');
        Route::post('/workspace/store', 'store')->name('workspace.store')->middleware('throttle:create-resources');
        Route::put('/workspace/{id}/update', 'update')->name('workspace.update');
        Route::delete('/workspace/{id}/delete', 'destroy')->name('workspace.delete');
        
        // Importação/Exportação de Workspaces    
        Route::get('/workspace/import/form', 'showImportForm')->name('workspace.import.form');
        Route::post('/workspace/import/process', 'import')->name('workspace.import');
        
        // Exportação (apenas para planos que permitem)
        Route::middleware(['check.plan:can_export'])->group(function () {
            Route::get('/workspace/{id}/export', 'export')->name('workspace.export');
            Route::get('/workspace/{id}/export/quick', 'exportQuick')->name('workspace.export.quick');
        });
    });
    
    Route::controller(WorkspaceSettingController::class)->group(function(){
        Route::get('/workspace/setting/{id}', 'index')->name('workspace.setting');
        Route::put('/workspace/setting/hash/{id}/update', 'generateNewHashApi')->name('workspace.update.generateNewHashApi');
        Route::put('/workspace/setting/password/{id}/update', 'passwordWorkspace')->name('workspace.update.passwordWorkspace');
        Route::put('/workspace/setting/view-workspace/{id}/update', 'viewWorkspace')->name('workspace.update.viewWorkspace');
        Route::post('/workspace/setting/{id}/duplicate', 'duplicate')->name('workspace.duplicate');
    });

    Route::controller(CollaboratorController::class)->group(function () {
        Route::get('/collaborations', 'indexCollaborations')->name('collaborations.index');
        Route::get('/collaborator/workspace/{workspaceId}', 'showCollaboration')->name('collaboration.show');
        Route::get('/workspace/collaborators/{workspaceId}', 'listCollaborators')->name('workspace.collaborators.list');
        Route::post('/workspace/collaborators/invite/{workspaceId}', 'inviteCollaborator')->name('workspace.collaborator.invite');
        Route::delete('/workspace/collaborators/{workspaceId}/{collaboratorId}', 'removeCollaborator')->name('workspace.collaborator.delete');
        Route::put('/workspace/collaborators/{collaborator}/role', 'updateCollaboratorRole')->name('workspace.collaborators.list.role.update');

        Route::post('/collaboration/accept/{token}', 'acceptInvite')->name('collaboration.invite.accept');
        Route::post('/collaboration/reject/{token}', 'rejectInvite')->name('collaboration.invite.reject');
        Route::post('/{id}/accept', 'acceptInviteById')->name('workspace.invite.accept.id');
        Route::post('/{id}/reject', 'rejectInviteById')->name('workspace.invite.reject.id');
    });

    Route::controller(FieldController::class)->group(function(){
        Route::post('/field/store', 'store')->name('field.store')->middleware('throttle:create-resources');
        Route::put('/field/{id}/update', 'update')->name('field.update');
        Route::delete('/field/{id}/destroy', 'destroy')->name('field.destroy');
        Route::post('/field/check-limit', 'checkLimit')->name('fields.checkLimit');
    });

    Route::controller(TopicController::class)->group(function(){
        Route::post('/topic/store', 'store')->name('topic.store')->middleware('throttle:create-resources');
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
        Route::put('/dashboard/profile/update', 'updateProfile')->name('user.profile.update');
        Route::put('/dashboard/profile/password/update', 'updatePassword')->name('user.profile.password.update');
    });

    Route::controller(SettingController::class)->group(function(){
        Route::get('/dashboard/settings', 'index')->name('dashboard.settings');
        Route::put('/dashboard/settings/update/hash', 'generateNewHashApi')->name('dashboard.settings.update.hash');
        Route::put('/settings/language', 'updateLanguage')->name('settings.language');
        Route::put('/settings/timezone', 'updateTimezone')->name('settings.timezone');
        Route::get('/settings', 'getSettings')->name('settings.get');
    });

    Route::controller(NotificationController::class)->group(function(){
        Route::post('/{id}/read','markAsRead')->name('notifications.markAsRead');
        Route::post('/read-all','markAllAsRead')->name('notifications.markAllAsRead');
        Route::get('/count','count')->name('notifications.count');
    });

    Route::controller(WorkspaceDomainController::class)->group(function () {
        // Apenas planos Pro podem usar domínios personalizados
        Route::middleware(['check.plan:can_use_api'])->group(function () {
            Route::post('workspaces/{workspace}/api/domains', 'addDomain')->name('workspace.api.domains.add');
            Route::delete('workspaces/{workspace}/api/domains', 'removeDomain')->name('workspace.api.domains.remove');
            Route::put('workspaces/{workspace}/api/domains/activate', 'activateDomain')->name('workspace.api.domains.activate');
            Route::put('workspaces/{workspace}/api/toggle', 'toggleApi')->name('workspace.api.toggle');
        });
    });

    // Rotas de Assinatura
    Route::controller(SubscriptionController::class)->group(function () {
        Route::get('/pricing', 'pricing')->name('subscription.pricing');
        Route::post('/checkout', 'checkout')->name('subscription.checkout');
        Route::get('/success', 'success')->name('subscription.success');
        Route::get('/cancel', 'cancel')->name('subscription.cancel');
        Route::post('/subscription/cancel', 'cancelSubscription')->name('subscription.cancel.subscription');
        Route::post('/subscription/resume', 'resumeSubscription')->name('subscription.resume');
        Route::get('/billing/portal', 'portal')->name('billing.portal');
    });

    // Gerenciamento de assinatura
    Route::controller(BillingController::class)->group(function () {
        Route::get('/billing', 'index')->name('billing.show');
        Route::post('/billing/payment-method/add', 'addPaymentMethod')->name('billing.payment-method.add');
        Route::post('/billing/payment-method/remove', 'removePaymentMethod')->name('billing.payment-method.remove');
        Route::post('/billing/payment-method/default', 'setDefaultPaymentMethod')->name('billing.payment-method.default');
        Route::post('/billing/plan/change', 'changePlan')->name('billing.plan.change');
        Route::get('/billing/invoice/{invoiceId}', 'downloadInvoice')->name('billing.invoice.download');
        Route::post('/billing/cancel', 'cancelSubscription')->name('billing.cancel');
        Route::post('/billing/resume', 'resumeSubscription')->name('billing.resume');
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
});

// Rota pública para GUI Mode
Route::get('/guimode', function (){ 
    return view('pages.dashboard.mode-api.interface-api'); 
})->name('guimode');

// Rotas de API com rate limiting baseado no plano
Route::middleware(['api.auth_token', 'api.log', 'plan.rate_limit'])->group(function(){
    Route::controller(ApiController::class)->group(function(){
        Route::post('/api/token/auth', 'getTokenByHashes')->name('api.token-auth');
        Route::get('/api/{id}/workspace', 'getWorkspaceData')->name('api.workspace');
        Route::get('/api/workspace/{id}/visible', 'getVisibleWorkspaceData')->name('api.workspace.visible');
        Route::get('/api/rate-limit-status', 'getRateLimitStatus')->name('api.rate-limit.status');
        
        // Novas rotas para informações do plano
        Route::get('/api/plan/limits', 'getPlanLimits')->name('api.plan.limits');
        Route::get('/api/subscription/status', 'getSubscriptionStatus')->name('api.subscription.status');
    });
});

// Rota pública para autenticação (com rate limiting)
Route::middleware(['api.log', 'plan.rate_limit'])->post('/api/token/auth', [ApiController::class, 'getTokenByHashes']);

// Rotas de localização
Route::get('/lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');