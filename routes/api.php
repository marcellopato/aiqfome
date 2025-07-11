<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;

// Rota pública de login
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware(['auth:sanctum'])->group(function () {
    // Retorna o usuário autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // CRUD de clientes
    Route::apiResource('clients', ClientController::class);

    // Aqui você pode adicionar outras rotas protegidas, como favoritos, etc.
});
