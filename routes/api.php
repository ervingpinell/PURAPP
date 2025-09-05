<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;

/**
 * ===========================
 * API PÚBLICA (sin auth)
 * ===========================
 * - GET: no requiere CSRF
 * - POST: están EXCEPTUADOS de CSRF en bootstrap/app.php
 */

// Viator Reviews (público)
Route::get('/reviews/{productCode}', [ReviewController::class, 'fetchReviewsGet'])
    ->name('api.reviews.get');

// Si tu JS necesita POST para payload complejos:
Route::post('/reviews', [ReviewController::class, 'fetchReviews'])
    ->name('api.reviews'); // EXCEPT en CSRF
Route::post('/reviews/batch', [ReviewController::class, 'fetchReviewsBatch'])
    ->name('api.reviews.batch'); // EXCEPT en CSRF

// Promo público (si de verdad quieres que sea público)
Route::post('/apply-promo', [PromoCodeController::class, 'apply'])
    ->name('api.promo.apply'); // EXCEPT en CSRF

// Contador reservado (si es público, déjalo aquí; si no, muévelo al grupo privado)
Route::get('/get-reserved', [CartController::class, 'getReserved'])
    ->name('api.cart.reserved');

// (Opcional) Throttle a lo público sensible
Route::middleware('throttle:20,1')->group(function () {
    // Ejemplo: podrías mover aquí /reviews si quieres limitar por IP
});


/**
 * ===========================
 * API PRIVADA (autenticada)
 * ===========================
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user())
        ->name('api.me');

    // Aquí pon cualquier endpoint que SOLO deba ver un usuario autenticado
    // Route::get('/bookings', [BookingApiController::class, 'index'])->name('api.bookings.index');
});
