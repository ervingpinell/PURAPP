<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Admin\API\TourDataController;
use App\Http\Controllers\Admin\API\BookingApiController;
use App\Http\Controllers\Admin\API\CapacityApiController;

// ============================
// API PÚBLICA (sin auth)
// ============================
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

        // Tours data (solo admin UI)
        Route::prefix('tours/{tour}')
            ->middleware(['throttle:tours-admin']) // p.ej. 120/min
            ->group(function () {
                Route::get('schedules',  [TourDataController::class, 'schedules'])->name('api.v1.tours.schedules');
                Route::get('languages',  [TourDataController::class, 'languages'])->name('api.v1.tours.languages');
                Route::get('categories', [TourDataController::class, 'categories'])->name('api.v1.tours.categories');
            });

        // Promo codes (admin)
        Route::get('bookings/verify-promo-code', [BookingApiController::class, 'verifyPromo'])
            ->middleware(['throttle:admin-light']) // p.ej. 120/min
            ->name('api.v1.bookings.verifyPromo');

        // Capacidad (admin)
        Route::prefix('capacity')->name('api.v1.capacity.')->group(function () {
            // Escrituras: menos permiso (por seguridad) pero alto para admin
            Route::patch('schedules/{schedule}/increase', [CapacityApiController::class, 'increase'])
                ->middleware(['throttle:capacity-admin']) // p.ej. 120/min
                ->name('increase');

            Route::patch('schedules/{schedule}/block',    [CapacityApiController::class, 'block'])
                ->middleware(['throttle:capacity-admin']) // p.ej. 120/min
                ->name('block');

            // Lecturas: más alto (panel usa varias consultas)
            Route::get('schedules/{schedule}/details',    [CapacityApiController::class, 'details'])
                ->middleware(['throttle:capacity-details']) // p.ej. 240/min
                ->name('details');
        });
    });
