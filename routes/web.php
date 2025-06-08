<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ReservaController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserRegisterController;
use App\Http\Controllers\Auth\ClienteRegisterController;

// Ruta pública
Route::get('/', function () {
    return view('welcome');
})->name('home');

//route for index
Route::view('/', 'index')->name('home');


// Rutas de login personalizadas
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas con middleware auth
Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Vista principal del admin
    Route::get('/admin', [HomeController::class, 'index'])->name('admin.home');

    // Rutas admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('tours', TourController::class);
        Route::get('reservas/pdf', [ReservaController::class, 'generarPDF'])->name('reservas.pdf');
        Route::get('reservas/{reserva}/comprobante', [ReservaController::class, 'generarComprobante'])->name('reservas.comprobante');
        Route::resource('reservas', ReservaController::class);
        Route::resource('users', UserRegisterController::class)->except(['show']);
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['show']);
    });

    // Perfil editable
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

});

// Registro de usuarios colaboradores (admin)
// Opcional: alias adicional para que coincida con AdminLTE
Route::get('/register', [ClienteRegisterController::class, 'create'])->name('register');
Route::post('/register', [ClienteRegisterController::class, 'store'])->name('register.store');

