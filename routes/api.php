<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\WorkspaceApiController;
use App\Http\Controllers\Api\TopicApiController;
use App\Http\Controllers\Api\FieldApiController;

// Rotas públicas (não precisam de verificação de API habilitada)
Route::post('/auth/login/token', [ApiController::class, 'getTokenByHashes'])->name('api.auth.login.token');
Route::post('/auth/login/token', [ApiController::class, 'getTokenByLogin'])->name('api.auth.login.token');

Route::get('/shared/{global_key_api}/{workspace_key_api}', [ApiController::class, 'sharedApi'])->name('workspace.shared-api');

// Health check (pública)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
})->name('api.health');


Route::middleware([
    'api.auth_token', 
    'plan.rate_limit',
    'check.api.enabled', 
    'check.api.domain'
])->group(function () {
    
    // Workspace endpoints
    Route::prefix('workspaces')->middleware('check.api.method:workspace')->group(function () {
        Route::get('/{workspaceId}', [WorkspaceApiController::class, 'show']);
        Route::get('/{workspaceId}/stats', [WorkspaceApiController::class, 'stats']);
        Route::get('/{workspaceId}/topics', [TopicApiController::class, 'index']);
        Route::post('/{workspaceId}/topics', [TopicApiController::class, 'store']);
        Route::put('/{workspaceId}', [WorkspaceApiController::class, 'update']);
        Route::patch('/{workspaceId}/settings', [WorkspaceApiController::class, 'updateSettings']);
    });

    // Topic endpoints
    Route::prefix('topics')->middleware('check.api.method:topics')->group(function () {
        Route::get('/{topicId}', [TopicApiController::class, 'show']);
        Route::put('/{topicId}', [TopicApiController::class, 'update']);
        Route::delete('/{topicId}', [TopicApiController::class, 'destroy']);
        Route::get('/{topicId}/fields', [FieldApiController::class, 'index']);
        Route::post('/{topicId}/fields', [FieldApiController::class, 'store']);
    });

    // Field endpoints
    Route::prefix('fields')->middleware('check.api.method:fields')->group(function () {
        Route::get('/{fieldId}', [FieldApiController::class, 'show']);
        Route::put('/{fieldId}', [FieldApiController::class, 'update']);
        Route::patch('/{fieldId}/visibility', [FieldApiController::class, 'updateVisibility']);
        Route::delete('/{fieldId}', [FieldApiController::class, 'destroy']);
    });
});