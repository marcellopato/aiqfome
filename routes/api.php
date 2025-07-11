<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FavoriteController;

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

    // Rotas de Favoritos
    Route::get('clients/{client}/favorites', [FavoriteController::class, 'index']);
    Route::post('clients/{client}/favorites', [FavoriteController::class, 'store']);
    Route::delete('clients/{client}/favorites/{product_id}', [FavoriteController::class, 'destroy']);

    // Listar todos os produtos disponíveis (proxy da Fake Store API)
    Route::get('products', [FavoriteController::class, 'listProducts']);

});
