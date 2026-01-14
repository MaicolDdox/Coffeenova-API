<?php

use App\Http\Controllers\Auth\TokenAuthController;
use App\Http\Controllers\CoffeeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShoppingCartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [TokenAuthController::class, 'register']);
    Route::post('/login', [TokenAuthController::class, 'login']);
});

// Catálogo público
Route::get('/coffees', [CoffeeController::class, 'index']);
Route::get('/coffees/{coffee}', [CoffeeController::class, 'show']);

// Rutas protegidas comunes (todos los autenticados)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [TokenAuthController::class, 'me']);
    Route::post('/auth/logout', [TokenAuthController::class, 'logout']);

    Route::get('/cart', [ShoppingCartController::class, 'show']);
    Route::post('/cart/items', [ShoppingCartController::class, 'addItem']);
    Route::put('/cart/items/{item}', [ShoppingCartController::class, 'updateItem']);
    Route::delete('/cart/items/{item}', [ShoppingCartController::class, 'removeItem']);
    Route::delete('/cart', [ShoppingCartController::class, 'clear']);

    Route::post('/cart/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    Route::post('/coffees', [CoffeeController::class, 'store']);
    Route::put('/coffees/{coffee}', [CoffeeController::class, 'update']);
    Route::delete('/coffees/{coffee}', [CoffeeController::class, 'destroy']);

    Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
    Route::get('/admin/orders/{order}', [OrderController::class, 'adminShow']);
});


