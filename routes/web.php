<?php

use App\Http\Controllers\ApiDomainController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\WorkspaceSettingController;
use App\Http\Controllers\WorkspaceSharedController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\EditRequestController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ApiManagementController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\RecordController;

use App\Http\Controllers\StripeWebhookController;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia; //new from structure

Route::middleware(['languages'])->group(function(){
    Route::get('/', function (){ return Inertia::render('Landing/Site', ['lang' => __('site')]); })->name('landing.handgeev');

    //Help Center
    Route::get('/help', [HelpController::class, 'index'])->name('help.center');
    
    // Termos e Privacidade
    Route::get('/terms', function () { return Inertia::render('Legal/Terms', ['lang' => __('site')]); })->name('legal.terms');
    Route::get('/privacy', function () { return Inertia::render('Legal/Privacy', ['lang' => __('site')]); })->name('legal.privacy');
    
    // Route::get('/teste', function (){ return view('pages.dashboard.teste'); })->name('teste.page');
    
    Route::controller(UserController::class)->group(function(){
        Route::post('/register/store', 'storeProfile')->name('register.store');
    });
    
    Route::get('/account/inactive', function (){ return view('pages.auth.inactive-account'); })->name('account.inactive');
    Route::get('/account/suspended', function (){ return view('pages.auth.suspended-account'); })->name('account.suspended');
    
    Route::controller(AccountController::class)->group(function(){
        Route::get('/login', 'indexLogin')->name('login.show');
        Route::post('/login/auth', 'authLogin')->name('login.auth');
        Route::get('/logout', 'logout')->name('logout');
        Route::get('/signup', 'indexRegister')->name('register.show');
        Route::get('/support/recovery-account', 'showRecovery')->name('recovery.account.show');
        Route::get('/support/recovery-password/{token}','showResetForm')->name('recovery.password.show');
        Route::post('/support/recovery-password','updatePasswordRecovery')->name('recovery.password.update');
    });
    
    Route::controller(EmailController::class)->group(function(){
        Route::get('/email/verify/code/form', 'showVerifyCodeEmail')->name('verify.code.email.show');
        Route::get('/email/confirm/form', 'showEmailConfirmForm')->name('email.confirm.form');
        Route::get('/email/confirm/{token}', 'confirmEmailChange')->name('email.confirm');
        Route::put('/email/update', 'updateEmail')->name('email.update');
        Route::post('/recovery-password/email', 'sendRecoveryAccountEmail')->name('recovery.password.email');
        Route::post('/email/verify/code', 'verifyEmail')->name('verification.verify');
        Route::post('/email/resend/code', 'resendVerifyEmail')->name('verification.resend');
        Route::post('/alterar-email', 'updateVerifyEmail')->name('verification.update-email');
    });

    // Webhook Stripe (sem CSRF protection)
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->withoutMiddleware([ValidateCsrfToken::class]);


    // Formulário de senha
    Route::controller(WorkspaceSharedController::class)->group(function(){
        Route::get('/shared/workspace/{global_key_api}/{workspace_key_api}/password','showPasswordForm')->name('workspace.shared.password');
        Route::post('/shared/workspace/{global_key_api}/{workspace_key_api}/verify-password','verifyPassword')->name('workspace.shared.verify-password');
        Route::get('/shared/workspace/{global_key_api}/{workspace_key_api}/api','sharedApi')->name('workspace.shared.api');
        Route::get('/shared/{workspace}/permissions', 'getPermissions')->name('api.get.permissions');
        Route::put('/shared/{workspace}/permissions', 'updatePermissions')->name('api.put.permissions');
        //Statistics
        Route::get('/shared/workspace/{global_key_api}/{workspace_key_api}/statistics', 'getApiStatistics')->name('api.get.statistics');
        Route::get('/shared/workspace/{global_key_api}/{workspace_key_api}/endpoint-statistics', 'getEndpointStatistics')->name('api.get.endpoint-statistics');
        // Rotas para visualização compartilhada
        Route::middleware(['workspace.api.password', 'check.api.access'])->group(function(){
            Route::get('/api/studio/workspace/{global_key_api}/{workspace_key_api}', 'geevStudio')->name('workspace.shared-geev-studio.show');
            Route::get('/api/rest/workspace/{global_key_api}/{workspace_key_api}', 'showApiRest')->name('workspace.api-rest.show');
        });
    }); 

    Route::get('/account/deactivated', function () { return view('pages.auth.account-deactivated');})->name('account.deactivated')->middleware('account.deactivated');
});


Route::middleware([
    'auth:web', 
    'languages', 
    'plan.limits', 
    'record.last.login',
    'check.user.suspended',
])->group(function(){
    Route::controller(WorkspaceController::class)->group(function(){
        Route::get('/workspaces', 'indexWorkspaces')->name('workspaces.show');
        Route::get('/workspace/{id}', 'index')->name('workspace.show');
        Route::post('/workspace/create', 'store')->name('workspace.create')->middleware('throttle:create-resources');
        Route::put('/workspace/{id}/update', 'update')->name('workspace.update');
        Route::delete('/workspace/{id}/delete', 'destroy')->name('workspace.delete');
        
        // Importação/Exportação de Workspaces    
        Route::get('/workspace/import/form', 'showImportForm')->name('workspace.import.form');
        Route::post('/workspace/import/process', 'import')->name('workspace.import');
        
        // Exportação (apenas para planos que permitem)
        Route::get('/workspace/{id}/export', 'export')->name('workspace.export');
        Route::get('/workspace/{id}/export/quick', 'exportQuick')->name('workspace.export.quick');
    });
    
    Route::controller(WorkspaceSettingController::class)->group(function(){
        Route::get('/workspace/setting/{id}', 'index')->name('workspace.setting');
        Route::put('/workspace/setting/hash/{id}/update', 'generateNewHashApi')->name('workspace.update.generateNewHashApi');
        Route::put('/workspace/setting/view-workspace/{id}/update', 'viewWorkspace')->name('workspace.update.viewWorkspace');
        Route::post('/workspace/setting/{id}/duplicate', 'duplicate')->name('workspace.duplicate');
        Route::put('/workspace/setting/{id}/access-settings', 'updateAccessSettings')->name('workspace.update.access-settings');
    });

    Route::controller(EditRequestController::class)->group(function(){
        Route::post('/edit-requests/{id}/approve', 'approveRequest')->name('edit-requests.approve');
        Route::post('/edit-requests/{id}/reject', 'rejectRequest')->name('edit-requests.reject');
        Route::get('/workspace/{id}/edit-requests', 'listPendingRequests')->name('workspace.edit-requests');
        Route::get('/workspace/{id}/edit-requests/history', 'listAllRequests')->name('workspace.edit-requests.history');
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

    Route::controller(TopicController::class)->group(function(){
        Route::post('/topic/store', 'store')->name('topic.store')->middleware('throttle:create-resources');
        Route::put('/topic/{id}', 'update')->name('topic.update');
        Route::delete('/topic/{id}/destroy', 'destroy')->name('topic.destroy');
        Route::post('/topic/{workspaceid}/merge-topics', 'mergeTopics')->name('workspace.merge-topics');
        
        Route::put('/topics/{id}/structure', 'updateTopicStructure')->name('topic.structure.update');
        Route::get('/topics/{topic}/export', 'export')->name('topics.export');
        Route::get('/topics/{topic}/download', 'download')->name('topics.download');
        Route::post('/workspaces/{workspace}/import-topic', 'import')->name('topics.import');
        Route::get('/importable-topics', 'importableTopics')->name('topics.importable');
        Route::post('/records', 'storeRecord')->name('records.store');
        Route::post('/topics/{topic}/add-structure-fields', 'addStructureFields')->name('topic.add-structure-fields');
    });

    Route::controller(StructureController::class)->group(function(){
        Route::get('/structures', 'index')->name('structures');
        Route::get('/structures/create', 'create')->name('structures.create');
        Route::get('/structures/available', 'getAvailableStructures')->name('structures.available');
        Route::post('/structures', 'store')->name('structures.store');
        
        // Rotas dinâmicas SEMPRE por último
        Route::get('/structures/{structure}', 'show')->name('structures.show');
        Route::get('/structures/{structure}/edit', 'edit')->name('structures.edit');
        Route::put('/structures/{structure}', 'update')->name('structures.update');
        Route::delete('/structures/{structure}', 'destroy')->name('structures.destroy');

        Route::get('/{structure}/export', 'export')->name('structures.export');
        Route::post('/import', 'import')->name('structures.import');
    });

    // === ADICIONE ESTE NOVO GRUPO PARA OS CAMPOS DA ESTRUTURA ===
    Route::controller(StructureFieldController::class)->group(function(){
        Route::post('/structures/{structure}/fields', 'store')->name('structure.fields.store');
        Route::put('/structures/{structure}/fields/{field}', 'update')->name('structure.fields.update');
        Route::delete('/structures/{structure}/fields/{field}', 'destroy')->name('structure.fields.destroy');
    });

    Route::controller(RecordController::class)->group(function(){
        Route::post('/records', 'store')->name('records.store');
        Route::put('/records/{record}/field/{field}', 'updateField')->name('record.field.update');
        Route::delete('/records/{record}', 'destroy')->name('records.destroy');
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
    
    Route::controller(AccountController::class)->group(function(){
        Route::delete('/account/delete', 'deleteAccount')->name('user.account.delete');
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

    // Rotas de Assinatura
    Route::controller(SubscriptionController::class)->group(function () {
        Route::get('/checkout/redirect', 'checkoutRedirect')->name('subscription.checkout.redirect');
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

    Route::controller(ApiManagementController::class)->group(function () {
        Route::get('/dashboard/my-apis', 'showMyApis')->name('management.apis.show');
        Route::put('/access/api/{workspace}/toggle', 'toggleAccessApi')->name('management.api.access.toggle');
        Route::put('/access/api/{workspace}/https-requirement/toggle', 'toggleHttpsRequirement')->name('workspace.api.https-requirement.toggle');
    });

    // Rotas de Administração
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {
        Route::controller(AdminController::class)->group(function () {
            // Gestão de usuários
            Route::get('/users', 'indexUsers')->name('admin.users');
            Route::get('/users/{id}/profile', 'userProfile')->name('admin.users.profile');
            Route::put('/users/{id}', 'updateUser')->name('admin.users.update');
            Route::delete('/users/{id}', 'deleteUser')->name('admin.users.delete');
            Route::post('/users/{id}/reset-password', 'resetPassword')->name('admin.users.reset-password');
            Route::post('/users/{id}/toggle-status', 'toggleUserStatus')->name('admin.users.toggle-status');
            Route::get('/users/{id}/activities', 'getUserActivities')->name('admin.users.activities');
            Route::get('/users/{id}/stats', 'getUserStats')->name('admin.users.stats');
            
            // Planos
            Route::get('/plans', 'plans')->name('admin.plans');
        });
    });
    
    Route::prefix('api/{workspace}')->group(function () {
        Route::controller(ApiDomainController::class)->group(function () {
            Route::post('/generate-api-key', 'generateApiKey')->name('workspace.generate-api-key');
            Route::post('/domains/add', 'addDomain')->name('workspace.api.domains.add');
            Route::delete('/domains/remove', 'removeDomain')->name('workspace.api.domains.remove');
            Route::put('/domain-restriction/toggle', 'toggleDomainRestriction')->name('workspace.api.domain-restriction.toggle');
            Route::put('/domains/activate', 'activateDomain')->name('workspace.api.domains.activate');
            Route::put('/jwt-requirement/toggle', 'toggleJwtRequirement')->name('workspace.api.jwt-requirement.toggle');
        });
    });
});

Route::middleware(['account.deactivated'])->group(function () {
    Route::controller(AccountController::class)->group(function(){
        Route::post('/account/restore', 'restoreAccount')->name('account.restore');
    });
});

//Lang
Route::get('/lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');