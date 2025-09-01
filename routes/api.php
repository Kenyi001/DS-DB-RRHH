<?php

use App\Http\Controllers\Api\EmpleadoController;
use App\Http\Controllers\Api\ContratoController;
use App\Http\Controllers\Api\PlanillaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'services' => [
            'database' => 'connected', // TODO: Implementar check real
            'redis' => 'connected',    // TODO: Implementar check real
        ]
    ]);
});

// API v1 Routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Empleados
    Route::apiResource('empleados', EmpleadoController::class, [
        'parameters' => ['empleados' => 'id']
    ]);
    
    // Contratos
    Route::apiResource('contratos', ContratoController::class, [
        'parameters' => ['contratos' => 'id']
    ]);
    Route::get('contratos/validar-solape', [ContratoController::class, 'validarSolape']);
    
    // Planilla
    Route::prefix('planilla')->controller(PlanillaController::class)->group(function () {
        Route::post('generar', 'generar');
        Route::get('status/{planillaId}', 'status');
        Route::post('preview', 'preview');
    });
    
});

// TODO: Implementar autenticación Sanctum
// Route::post('/auth/login', [AuthController::class, 'login']);
// Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');