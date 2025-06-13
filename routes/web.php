<?php

use Illuminate\Support\Facades\Route;

// Dashboard
use App\Http\Controllers\DashBoardController;

// Bookings
use App\Http\Controllers\Admin\Bookings\ReservaController;

// Tours
use App\Http\Controllers\Admin\Tours\TourController;

// Categories
use App\Http\Controllers\Admin\Categories\CategoryController;

// Languages
use App\Http\Controllers\Admin\Languages\TourLanguageController;

// Users (Admin)
use App\Http\Controllers\Admin\Users\UserRegisterController;
use App\Http\Controllers\Admin\Users\RoleController;
use App\Http\Controllers\Admin\ProfileController;

// Auth (General)
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ClienteRegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;

// Profile (User)
use App\Http\Controllers\User\UserProfileController;


        // Página pública principal
        Route::get('/', [DashBoardController::class, 'index'])->name('home');
        Route::get('/idioma/{idioma}', [DashBoardController::class, 'cambiarIdioma'])->name('cambiar.idioma');

        // Login personalizado
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);

        // Registro de clientes
        Route::get('/register', [ClienteRegisterController::class, 'create'])->name('register');
        Route::post('/register', [ClienteRegisterController::class, 'store'])->name('register.store');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Rutas protegidas con autenticación
        Route::middleware(['auth'])->group(function () {

            // Vista principal del panel admin
            Route::get('/admin', [DashBoardController::class, 'dashboard'])->name('admin.home');

            // Rutas del panel admin
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::resource('tours', TourController::class);
                Route::get('reservas/pdf', [ReservaController::class, 'generarPDF'])->name('reservas.pdf');
                Route::get('reservas/{reserva}/comprobante', [ReservaController::class, 'generarComprobante'])->name('reservas.comprobante');
                Route::resource('reservas', ReservaController::class);
                Route::resource('users', UserRegisterController::class)->except(['show']);
                Route::resource('roles', RoleController::class)->except(['show']);
                Route::resource('categories', CategoryController::class)->except(['show']);
                Route::resource('languages', TourLanguageController::class)->except(['show']);
            });

            // Perfil del admin
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

            // Perfil del cliente
            Route::get('/profile-user', [UserProfileController::class, 'edit'])->name('user.profile.edit');
            Route::post('/profile-user', [UserProfileController::class, 'update'])->name('user.profile.update');
        });
