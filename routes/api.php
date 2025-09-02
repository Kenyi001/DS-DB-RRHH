<?php

use App\Modules\Empleados\Controllers\Api\EmpleadoApiController;
use App\Modules\Contratos\Controllers\Api\ContratoApiController;
use App\Modules\Planillas\Controllers\Api\PlanillaApiController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (Sin autenticación requerida)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Perfil de usuario
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::get('profile', 'profile');
        Route::post('change-password', 'changePassword');
    });

    // Usuario actual
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API v1 Routes (Con autenticación)
    Route::prefix('v1')->group(function () {
        
        // Empleados - Acceso diferenciado por roles
        Route::middleware('role:admin,manager')->group(function () {
            Route::post('empleados', [EmpleadoApiController::class, 'store']); // Solo admin/manager pueden crear
            Route::put('empleados/{id}', [EmpleadoApiController::class, 'update']); // Solo admin/manager pueden editar
            Route::delete('empleados/{id}', [EmpleadoApiController::class, 'destroy']); // Solo admin/manager pueden eliminar
        });
        
        Route::middleware('role:admin,manager,user')->group(function () {
            Route::get('empleados', [EmpleadoApiController::class, 'index']); // Todos pueden ver lista
            Route::get('empleados/{id}', [EmpleadoApiController::class, 'show']); // Todos pueden ver detalles
            Route::get('empleados/estadisticas', [EmpleadoApiController::class, 'estadisticas']);
            Route::get('empleados/search', [EmpleadoApiController::class, 'search']);
            Route::get('empleados/activos', [EmpleadoApiController::class, 'activos']);
        });
    
        // Contratos - Rutas con autorización por rol
        Route::middleware('role:admin,manager')->group(function () {
            Route::post('contratos', [ContratoApiController::class, 'store']); // Solo admin/manager pueden crear
            Route::put('contratos/{id}', [ContratoApiController::class, 'update']); // Solo admin/manager pueden editar  
            Route::delete('contratos/{id}', [ContratoApiController::class, 'destroy']); // Solo admin/manager pueden eliminar
            Route::post('contratos/{id}/reactivar', [ContratoApiController::class, 'reactivar']);
            Route::post('contratos/{id}/finalizar', [ContratoApiController::class, 'finalizar']);
            Route::post('contratos/{id}/renovar', [ContratoApiController::class, 'renovar']);
        });
        
        Route::middleware('role:admin,manager,user')->group(function () {
            Route::get('contratos', [ContratoApiController::class, 'index']); // Todos pueden ver lista
            Route::get('contratos/estadisticas', [ContratoApiController::class, 'estadisticas']);
            Route::get('contratos/vigentes', [ContratoApiController::class, 'vigentes']);
            Route::get('contratos/search', [ContratoApiController::class, 'search']);
            Route::get('contratos/{id}', [ContratoApiController::class, 'show']); // Todos pueden ver detalles
            Route::get('empleados/{empleadoId}/contratos', [ContratoApiController::class, 'contratosPorEmpleado']);
            Route::get('empleados/{empleadoId}/contrato-vigente', [ContratoApiController::class, 'contratoVigenteEmpleado']);
        });
    
        // Planillas - Rutas con autorización por rol
        Route::middleware('role:admin,manager')->group(function () {
            Route::post('planillas/generar', [PlanillaApiController::class, 'generar']);
            Route::put('planillas/{id}', [PlanillaApiController::class, 'update']);
            Route::post('planillas/{id}/pagar', [PlanillaApiController::class, 'marcarComoPagada']);
            Route::post('planillas/{id}/anular', [PlanillaApiController::class, 'anular']);
        });
        
        Route::middleware('role:admin,manager,user')->group(function () {
            Route::get('planillas', [PlanillaApiController::class, 'index']);
            Route::get('planillas/estadisticas', [PlanillaApiController::class, 'estadisticas']);
            Route::get('planillas/periodo/{gestion}/{mes}', [PlanillaApiController::class, 'porPeriodo']);
            Route::get('planillas/reporte/{gestion}/{mes}', [PlanillaApiController::class, 'reporte']);
            Route::get('planillas/{id}', [PlanillaApiController::class, 'show']);
            Route::get('empleados/{empleadoId}/planillas', [PlanillaApiController::class, 'planillasEmpleado']);
        });
    
    });
});