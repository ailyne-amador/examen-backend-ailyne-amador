<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CamisetaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TallaController;

// Tallas
Route::apiResource('tallas', TallaController::class);

// Clientes
Route::apiResource('clientes', ClienteController::class);
Route::get('clientes/{cliente_id}/camisetas', [CamisetaController::class, 'porCliente']);

// Camisetas
Route::apiResource('camisetas', CamisetaController::class);
Route::get('camisetas/{id}/precio', [CamisetaController::class, 'precioFinal']);
