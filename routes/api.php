<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\ReviewController;

Route::get('/reviews/{productCode}', [ReviewController::class, 'fetchReviewsGet'])->name('api.reviews.get');
Route::post('/reviews', [ReviewController::class, 'fetchReviews'])->name('api.reviews');
Route::post('/reviews/batch', [ReviewController::class, 'fetchReviewsBatch'])->name('api.reviews.batch');
// Rutas de la API para códigos promocionales
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;

Route::get('/get-reserved', [CartController::class, 'getReserved']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas de la API para códigos promocionales
Route::post('/apply-promo', [PromoCodeController::class, 'apply']);
