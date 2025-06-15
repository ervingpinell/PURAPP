<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;

// Controllers
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Admin\Bookings\ReservaController;
use App\Http\Controllers\Admin\Tours\TourController;
use App\Http\Controllers\Admin\Tours\TourScheduleController;
use App\Http\Controllers\Admin\Tours\TourAvailabilityController;
use App\Http\Controllers\Admin\Categories\CategoryController;
use App\Http\Controllers\Admin\Languages\TourLanguageController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\Admin\Users\UserRegisterController;
use App\Http\Controllers\Admin\Users\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ClienteRegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;


Route::middleware([SetLocale::class])->group(function () {

    // Rutas públicas
    Route::get('/', [DashBoardController::class, 'index'])->name('home');
    Route::get('/language/{language}', [DashBoardController::class, 'switchLanguage'])->name('switch.language');

    // Autenticación
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [ClienteRegisterController::class, 'create'])->name('register');
    Route::post('/register', [ClienteRegisterController::class, 'store'])->name('register.store');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Verificación de correo (opcional)
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->middleware('auth')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/email/resend', [VerifyEmailController::class, 'resend'])->middleware('auth')->name('verification.resend');

    // Perfil de usuario normal (todos los usuarios autenticados)
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Panel administrativo (solo admins y roles permitidos)
    Route::middleware(['auth', 'CheckRole'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard admin
        Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

        // Perfil admin (opcional: rutas separadas para editar perfil admin)
        Route::get('/profile', [ProfileController::class, 'adminShow'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'adminEdit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'adminUpdate'])->name('profile.update');

        // Gestión de tours
        Route::resource('tours', TourController::class);

        // Gestión de horarios y disponibilidad
        Route::prefix('tours')->name('tours.')->group(function () {
            Route::resource('schedule', TourScheduleController::class);
            Route::resource('availability', TourAvailabilityController::class);
        });

        // Reservas y PDF/comprobante
        Route::get('reservas/pdf', [ReservaController::class, 'generarPDF'])->name('reservas.pdf');
        Route::get('reservas/{reserva}/comprobante', [ReservaController::class, 'generarComprobante'])->name('reservas.comprobante');
        Route::resource('reservas', ReservaController::class);

        // Usuarios y roles
        Route::resource('users', UserRegisterController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);

        // Categorías, idiomas, amenidades
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('languages', TourLanguageController::class)->except(['show']);
        Route::resource('amenities', AmenityController::class);
    });

});
