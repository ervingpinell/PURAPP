<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ReservaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserRegisterController;

// Ruta pública
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rutas protegidas con middleware auth
Route::middleware(['auth'])->group(function () {
    // Redirección de configuración
    Route::redirect('settings', 'settings/profile');

    // Rutas de configuración con Livewire Volt
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Ruta al dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Vista principal del admin
    Route::get('/admin', [HomeController::class, 'index'])->name('admin.home');

    // Grupo de rutas admin
    Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('clientes', ClienteController::class);
    Route::resource('tours', TourController::class);
    
    // PDF general de reservas
    Route::get('reservas/pdf', [ReservaController::class, 'generarPDF'])->name('reservas.pdf');

    // PDF de comprobante por reserva
    Route::get('reservas/{reserva}/comprobante', [ReservaController::class, 'generarComprobante'])->name('reservas.comprobante');


    // Recurso de reservas al final
    Route::resource('reservas', ReservaController::class);

    });

});

// Rutas de login personalizadas
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas de registro personalizadas
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
});

//Rutas para registro de usuarios(colaboradores)
// Solo se puede acceder a estas rutas si el usuario está autenticado
Route::middleware(['auth'])->group(function () {
    Route::get('/register', [App\Http\Controllers\Admin\UserRegisterController::class, 'create'])->name('register.create');
    Route::post('/register', [App\Http\Controllers\Admin\UserRegisterController::class, 'store'])->name('register.store');
});
