<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Reviews\ReviewApiController;

// ============================
// API PÚBLICA (sin auth)
// ============================


// Otros endpoints públicos (no hace falta noindex en POST)
Route::post('/apply-promo', [PromoCodeController::class, 'apply'])
    ->name('api.promo.apply'); // EXCEPT en CSRF

Route::get('/get-reserved', [CartController::class, 'getReserved'])
    ->name('api.cart.reserved');

// ============================
// API PRIVADA (auth)
// ============================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn ($request) => $request->user())->name('api.me');
});
