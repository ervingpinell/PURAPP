<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Reviews\ReviewApiController;
use App\Http\Controllers\Admin\API\TourDataController;
use App\Http\Controllers\Admin\API\BookingApiController;

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


Route::prefix('v1')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        // Tours data (solo para admin)
        Route::prefix('tours/{tour}')->group(function () {
            Route::get('schedules',  [TourDataController::class, 'schedules'])->name('api.v1.tours.schedules');
            Route::get('languages',  [TourDataController::class, 'languages'])->name('api.v1.tours.languages');
            Route::get('categories', [TourDataController::class, 'categories'])->name('api.v1.tours.categories');
        });

        // Promo codes
        Route::get('bookings/verify-promo-code', [BookingApiController::class, 'verifyPromo'])
            ->name('api.v1.bookings.verifyPromo');
    });

