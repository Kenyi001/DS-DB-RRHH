<?php

use Illuminate\Support\Facades\Route;

// Ruta temporal para evitar errores
Route::get('/', function() {
    return response()->json([
        'message' => 'YPFB RRHH API System',
        'version' => '1.0.0',
        'api_url' => url('/api'),
        'docs' => 'Use /api endpoints for API access'
    ]);
})->name('home');

// Ruta login temporal para evitar errores de redirección
Route::get('/login', function() {
    return response()->json([
        'message' => 'Use POST /api/auth/login for API authentication'
    ]);
})->name('login');