<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ReservaController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserRegisterController;
use App\Http\Controllers\Auth\ClienteRegisterController;
use App\Http\Controllers\User\UserProfileController;

//route for index
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/idioma/{idioma}', [HomeController::class, 'cambiarIdioma'])->name('cambiar.idioma');




// Rutas de login personalizadas
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);


// Rutas protegidas con middleware auth
Route::middleware(['auth'])->group(function () {

    // Vista principal del admin
    Route::get('/admin', [HomeController::class, 'dashboard'])->name('admin.home');

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
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//route for the users can edit they profiles 
Route::middleware(['auth'])->group(function () {
    Route::get('/profile-user', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::post('/profile-user', [UserProfileController::class, 'update'])->name('user.profile.update');
});

