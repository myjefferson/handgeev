<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\WorkspaceApiController;
use App\Http\Controllers\Api\StructureApiController;
use App\Http\Controllers\Api\TopicApiController;
use App\Http\Controllers\Api\RecordApiController;
use App\Http\Controllers\Api\FieldApiController;

/*
|--------------------------------------------------------------------------
| Rotas públicas (sem autenticação)
|--------------------------------------------------------------------------
*/

// Autenticação via token
Route::post('/auth/login/token', [ApiController::class, 'getTokenByLogin']); //OK!!
// Route::post('/auth/login/token/hash', [ApiController::class, 'getTokenByHashes']);

// API pública (consumo externo)
Route::get(
    '/shared/{global_key_api}/{workspace_key_api}',
    [ApiController::class, 'sharedApi']
)->name('workspace.shared-api');

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
})->name('api.health');



/*
|--------------------------------------------------------------------------
| Rotas autenticadas (API privada)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'api.auth_token',
    'plan.rate_limit',
    'check.api.enabled',
])->prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | WORKSPACES
    |--------------------------------------------------------------------------
    */
    Route::prefix('workspaces')
        ->middleware('check.api.method:workspace')
        ->group(function () {

        Route::get('/', [WorkspaceApiController::class, 'index']); // opcional //OK
        Route::get('{workspace}', [WorkspaceApiController::class, 'show']); //OK
        Route::get('{workspace}/stats', [WorkspaceApiController::class, 'stats']); //OK
        Route::put('{workspace}', [WorkspaceApiController::class, 'update']); //
        Route::patch('{workspace}/settings', [WorkspaceApiController::class, 'updateSettings']); //

        /*
        |--------------------------------------------------------------------------
        | STRUCTURES (dentro do workspace)
        |--------------------------------------------------------------------------
        */
        Route::prefix('{workspace}/structures')->group(function () {
            Route::get('/', [StructureApiController::class, 'index']); //OK
        });
    });
    

    
    /*
    |--------------------------------------------------------------------------
    | STRUCTURES (operações diretas)
    |--------------------------------------------------------------------------
    */
    Route::prefix('structures')
        ->middleware('check.api.method:structures')
        ->group(function () {
        
        Route::get('{structure}', [StructureApiController::class, 'show']); //OK
        Route::put('{structure}', [StructureApiController::class, 'update']);
        Route::post('/create', [StructureApiController::class, 'store']); //OK
        Route::delete('{structure}', [StructureApiController::class, 'destroy']); //OK

        /*
        |--------------------------------------------------------------------------
        | TOPICS (dentro da structure)
        |--------------------------------------------------------------------------
        */
        Route::prefix('{structure}/topics')->group(function () {
            Route::get('/', [TopicApiController::class, 'index']); //OK
            Route::post('/', [TopicApiController::class, 'store']); //
        });
    });



    /*
    |--------------------------------------------------------------------------
    | TOPICS (operações diretas)
    |--------------------------------------------------------------------------
    */
    Route::prefix('topics')
        ->middleware('check.api.method:topics')
        ->group(function () {

        Route::get('{topic}', [TopicApiController::class, 'show']); //OK
        Route::put('{topic}', [TopicApiController::class, 'update']); //
        Route::delete('{topic}', [TopicApiController::class, 'destroy']); //

        /*
        |--------------------------------------------------------------------------
        | RECORDS (dados reais)
        |--------------------------------------------------------------------------
        */
        Route::prefix('{topic}/records')->group(function () {
            Route::get('/', [RecordApiController::class, 'index']);   // 🔥 principal endpoint //OK
            Route::post('/', [RecordApiController::class, 'store']);    //
        });
    });



    /*
    |--------------------------------------------------------------------------
    | RECORDS (operações diretas)
    |--------------------------------------------------------------------------
    */
    Route::prefix('records')
        ->middleware('check.api.method:records')
        ->group(function () {

        Route::get('{record}', [RecordApiController::class, 'show']); //OK
        Route::put('{record}', [RecordApiController::class, 'update']); //
        Route::delete('{record}', [RecordApiController::class, 'destroy']); //

        /*
        |--------------------------------------------------------------------------
        | FIELDS (dentro do record)
        |--------------------------------------------------------------------------
        */
        Route::prefix('{record}/fields')->group(function () {
            Route::get('/', [FieldApiController::class, 'index']);
            Route::post('/', [FieldApiController::class, 'store']);
        });
    });



    /*
    |--------------------------------------------------------------------------
    | FIELDS (operações diretas)
    |--------------------------------------------------------------------------
    */
    Route::prefix('fields')
        ->middleware('check.api.method:fields')
        ->group(function () {

        Route::get('{field}', [FieldApiController::class, 'show']);
        Route::put('{field}', [FieldApiController::class, 'update']);
        Route::patch('{field}/visibility', [FieldApiController::class, 'updateVisibility']);
        Route::delete('{field}', [FieldApiController::class, 'destroy']);
    });
});