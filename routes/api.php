<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Cart\CartController;

// Rutas de la API para códigos promocionales
use App\Http\Controllers\Api\PromoCodeController;

Route::get('/get-reserved', [CartController::class, 'getReserved']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas de la API para códigos promocionales
Route::post('/apply-promo', [PromoCodeController::class, 'apply']);
