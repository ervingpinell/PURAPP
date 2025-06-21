<?php

use App\Http\Controllers\Admin\Bookings\BookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;

// Controllers públicos y de autenticación
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ClienteRegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;

// Controllers del panel admin
use App\Http\Controllers\Admin\Users\UserRegisterController;
use App\Http\Controllers\Admin\Users\RoleController;
use App\Http\Controllers\Admin\Languages\TourLanguageController;
use App\Http\Controllers\Admin\Tours\TourController;
use App\Http\Controllers\Admin\Tours\TourScheduleController;
use App\Http\Controllers\Admin\Tours\TourAvailabilityController;
use App\Http\Controllers\Admin\Tours\AmenityController;
use App\Http\Controllers\Admin\Tours\ItineraryItemController;
use App\Http\Controllers\Admin\Tours\ItineraryController;
use App\Http\Controllers\Admin\Tours\TourTypeController;
use App\Http\Controllers\Admin\Cart\CartController;

Route::middleware([SetLocale::class])->group(function () {

    // 🌐 Rutas públicas
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/language/{language}', [DashBoardController::class, 'switchLanguage'])->name('switch.language');

    // 🔐 Autenticación (Clientes)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [ClienteRegisterController::class, 'create'])->name('register');
    Route::post('/register', [ClienteRegisterController::class, 'store'])->name('register.store');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // 📧 Verificación de correo (opcional)
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->middleware('auth')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/email/resend', [VerifyEmailController::class, 'resend'])->middleware('auth')->name('verification.resend');

    // 👤 Perfil cliente autenticado
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
    });

    // 🛠 Panel administrativo (solo para roles permitidos)
    Route::middleware(['auth', 'CheckRole'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard administrador
        Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

        // Perfil admin
        Route::get('/profile', [ProfileController::class, 'adminShow'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'adminEdit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'adminUpdate'])->name('profile.update');

        // Módulo Tours
      Route::resource('tours', TourController::class)->except(['create', 'edit', 'show']);

        // Submódulos de tours agrupados
        Route::prefix('tours')->name('tours.')->group(function () {

            // Horarios
            Route::get('schedule', [TourScheduleController::class, 'index'])->name('schedule.index');
            Route::post('schedule', [TourScheduleController::class, 'store'])->name('schedule.store');
            Route::put('schedule/{schedule}', [TourScheduleController::class, 'update'])->name('schedule.update');
            Route::delete('schedule/{schedule}', [TourScheduleController::class, 'destroy'])->name('schedule.destroy');
            Route::put('schedule/{schedule}/toggle', [TourScheduleController::class, 'toggle'])->name('schedule.toggle');
            
            // Itinerarios
            Route::resource('itinerary', ItineraryController::class)->except(['show']);
            Route::post('itinerary/{itinerary}/assign-items', [ItineraryController::class, 'assignItems'])->name('itinerary.assignItems');

            // Ítems de itinerario
            Route::resource('itinerary_items', ItineraryItemController::class)->except(['show', 'create', 'edit']);

            // Disponibilidad
            Route::resource('availability', TourAvailabilityController::class)->except(['show']);

            // Amenidades
            Route::resource('amenities', AmenityController::class)->except(['show']);
        });

        // Reservaciones y comprobantes
        Route::get('reservas/pdf', [BookingController::class, 'generarPDF'])->name('reservas.pdf');
        Route::get('reservas/{reserva}/comprobante', [BookingController::class, 'generarComprobante'])->name('reservas.comprobante');
        Route::resource('reservas', BookingController::class);

        // Gestión de usuarios y roles
        Route::resource('users', UserRegisterController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);

        // Categorías, idiomas y tipos de tour
    Route::resource('tourtypes', TourTypeController::class, [
        'parameters' => ['tourtypes' => 'tourType']
    ])->except(['show']);
    Route::put('tourtypes/{tourType}/toggle', [TourTypeController::class, 'toggle'])->name('tourtypes.toggle');


Route::resource('languages', TourLanguageController::class, [
    'parameters' => ['languages' => 'language']
])->except(['show']);

        Route::resource('amenities', AmenityController::class); // Doble, si se usa en otro contexto

        
        // ----------- Carrito (Clientes) -----------
        Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
        Route::post('/carrito', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/carrito/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::post('/carrito/item/{item}/update', [CartController::class, 'updateFromPost'])->name('cart.updateFromPost');
        Route::delete('/carrito/item/{item}', [CartController::class, 'destroy'])->name('cart.item.destroy');

        // ----------- Carrito (Administración) -----------
        Route::get('/carritos-todos', [CartController::class, 'allCarts'])->name('cart.general');
        Route::delete('/admin/carrito/item/{item}', [CartController::class, 'destroy'])->name('admin.cart.item.destroy');

        // ----------- Confirmar reservas -----------
        Route::post('/reservas/from-cart', [BookingController::class, 'storeFromCart'])->name('reservas.storeFromCart');



    });

});