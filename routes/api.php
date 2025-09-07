<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;

// ============================
// API PÚBLICA (sin auth)
// ============================

// 👇 Todo lo de REVIEWS NO debe indexarse: HTML ni JSON
Route::middleware(['noindex','throttle:60,1'])->group(function () {
    Route::get('/reviews/{productCode}', [ReviewController::class, 'fetchReviewsGet'])
        ->name('api.reviews.get');

    // Si tu JS necesita POST para payloads complejos:
    Route::post('/reviews', [ReviewController::class, 'fetchReviews'])
        ->name('api.reviews'); // EXCEPT en CSRF
    Route::post('/reviews/batch', [ReviewController::class, 'fetchReviewsBatch'])
        ->name('api.reviews.batch'); // EXCEPT en CSRF
});

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
