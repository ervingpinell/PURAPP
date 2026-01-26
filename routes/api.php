<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Reviews\ReviewApiController;
use App\Http\Controllers\Admin\API\ProductDataController;
use App\Http\Controllers\Admin\API\BookingApiController;
use App\Http\Controllers\Admin\API\CapacityApiController;


// ============================
// API PÚBLICA (sin auth)
// ============================


// Otros endpoints públicos (no hace falta noindex en POST)
Route::post('/apply-promo', [PromoCodeController::class, 'apply'])
    ->name('api.promo.apply'); // EXCEPT en CSRF

Route::get('/get-reserved', [CartController::class, 'getReserved'])
    ->name('api.cart.reserved');

// Debug endpoint for Alignet (development only)
Route::post('/debug/alignet-request', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('🔍 ALIGNET DEBUG - Frontend Request Data', [
        'payload' => $request->input('payload'),
        'baseUrl' => $request->input('baseUrl'),
        'timestamp' => $request->input('timestamp'),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    return response()->json(['status' => 'logged']);
})->middleware('web');

// ============================
// API PRIVADA (auth)
// ============================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn($request) => $request->user())->name('api.me');
});


Route::prefix('v1')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        // Tours data (solo para admin)
        Route::prefix('tours/{tour}')->group(function () {
            Route::get('schedules',  [ProductDataController::class, 'schedules'])->name('api.v1.tours.schedules');
            Route::get('languages',  [ProductDataController::class, 'languages'])->name('api.v1.tours.languages');
            Route::get('categories', [ProductDataController::class, 'categories'])->name('api.v1.tours.categories');
        });

        // Promo codes
        Route::get('bookings/verify-promo-code', [BookingApiController::class, 'verifyPromo'])
            ->name('api.v1.bookings.verifyPromo');

        //       Route::prefix('capacity')->name('api.v1.capacity.')->group(function () {
        //     Route::patch('schedules/{schedule}/increase', [CapacityApiController::class, 'increase'])
        //         ->name('increase'); // api.v1.capacity.increase
        //     Route::patch('schedules/{schedule}/block',    [CapacityApiController::class, 'block'])
        //         ->name('block');   // api.v1.capacity.block
        //     Route::get('schedules/{schedule}/details',    [CapacityApiController::class, 'details'])
        //         ->name('details'); // api.v1.capacity.details
        // });
    });
