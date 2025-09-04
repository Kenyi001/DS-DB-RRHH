<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AuthController;
use App\Modules\Empleados\Controllers\Web\EmpleadoWebController;
use App\Modules\Contratos\Controllers\Web\ContratoWebController;
use App\Modules\Planillas\Controllers\Web\PlanillaWebController;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Dashboard Principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Módulo Empleados
    Route::prefix('empleados')->name('empleados.')->group(function () {
        Route::get('/', [EmpleadoWebController::class, 'index'])->name('index');
        Route::get('/create', [EmpleadoWebController::class, 'create'])->name('create');
        Route::post('/', [EmpleadoWebController::class, 'store'])->name('store');
        Route::get('/{id}', [EmpleadoWebController::class, 'show'])->name('show')->where('id', '[0-9]+');
        Route::get('/{id}/edit', [EmpleadoWebController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        Route::put('/{id}', [EmpleadoWebController::class, 'update'])->name('update')->where('id', '[0-9]+');
        Route::delete('/{id}', [EmpleadoWebController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
    });
    
    // Módulo Contratos
    Route::prefix('contratos')->name('contratos.')->group(function () {
        Route::get('/', [ContratoWebController::class, 'index'])->name('index');
        Route::get('/create', [ContratoWebController::class, 'create'])->name('create');
        Route::post('/', [ContratoWebController::class, 'store'])->name('store');
        
        // Rutas especiales - Con nombres explícitos para evitar conflictos
        Route::get('/vigentes', [ContratoWebController::class, 'vigentes'])->name('vigentes');
        Route::get('/alertas', [ContratoWebController::class, 'alertas'])->name('alertas');
        
        // Rutas genéricas con parámetros - Con restricción numérica
        Route::get('/{id}', [ContratoWebController::class, 'show'])->name('show')->where('id', '[0-9]+');
        Route::get('/{id}/edit', [ContratoWebController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        Route::put('/{id}', [ContratoWebController::class, 'update'])->name('update')->where('id', '[0-9]+');
        Route::delete('/{id}', [ContratoWebController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
    });
    
    // Módulo Planillas
    Route::prefix('planillas')->name('planillas.')->group(function () {
        Route::get('/', [PlanillaWebController::class, 'index'])->name('index');
        
        // Rutas especiales - DEBEN ir ANTES que las rutas genéricas
        Route::get('/generar', [PlanillaWebController::class, 'generar'])->name('generar');
        Route::post('/generar', [PlanillaWebController::class, 'procesarGeneracion'])->name('generar.post');
        Route::get('/reportes', [PlanillaWebController::class, 'reportes'])->name('reportes');
        
        // Rutas genéricas con parámetros - Con restricción numérica
        Route::get('/{id}', [PlanillaWebController::class, 'show'])->name('show')->where('id', '[0-9]+');
    });
    
    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [DashboardController::class, 'reportes'])->name('dashboard');
    });
    
    // Configuración
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        Route::get('/', [DashboardController::class, 'configuracion'])->name('index');
        Route::get('/perfil', [DashboardController::class, 'perfil'])->name('perfil');
    });
    
    // Administración (solo admins)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios', [DashboardController::class, 'usuarios'])->name('usuarios');
        Route::get('/sistema', [DashboardController::class, 'sistema'])->name('sistema');
    });
    
    // Perfil de usuario
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::get('/', [DashboardController::class, 'miPerfil'])->name('index');
    });
});