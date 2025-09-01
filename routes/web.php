<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PlanillaController;
use App\Http\Controllers\ReporteController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('empleados', EmpleadoController::class);
Route::resource('planillas', PlanillaController::class);
Route::resource('reportes', ReporteController::class);

Route::get('planillas/{planilla}/generar', [PlanillaController::class, 'generar'])->name('planillas.generar');
Route::post('planillas/{planilla}/procesar', [PlanillaController::class, 'procesar'])->name('planillas.procesar');