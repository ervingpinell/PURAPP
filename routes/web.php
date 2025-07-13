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
use App\Http\Controllers\Admin\Bookings\HotelListController;
use App\Http\Controllers\Admin\Tours\TourExcludedDateController;

// Controlador para los emails
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;


Route::middleware([SetLocale::class])->group(function () {

    // 🌐 Rutas públicas
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/language/{language}', [DashBoardController::class, 'switchLanguage'])->name('switch.language');

    // ✅ Test Email
    Route::get('/send-test-email', function () {
        $booking = App\Models\Booking::latest()->with(['user','detail','tour'])->first();
        Mail::to($booking->user->email)->send(new App\Mail\BookingCreatedMail($booking));
        return 'Correo enviado!';
    });

    // ✅ Agregar tour al carrito
    // ✅ Ruta del carrito del cliente (afuera de prefix('admin'))
    Route::middleware(['auth'])->group(function () {
        Route::get('/mi-carrito', [CartController::class, 'index'])->name('public.cart.index');
    });
    Route::post('/carrito/agregar/{tour}', [CartController::class, 'store'])
        ->middleware('auth')
        ->name('carrito.agregar');
        
    Route::post('/reservas/from-cart', [BookingController::class, 'storeFromCart'])
    ->middleware('auth')
    ->name('public.reservas.storeFromCart');



    // ✅ Autenticación
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [ClienteRegisterController::class, 'create'])->name('register');
    Route::post('/register', [ClienteRegisterController::class, 'store'])->name('register.store');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ✅ Tour público
    Route::get('/tours/{id}', [HomeController::class, 'showTour'])->name('tours.show');
    Route::get('/tours', [HomeController::class, 'allTours'])->name('tours.index');

    // ✅ Verificación de correo
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->middleware('auth')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/email/resend', [VerifyEmailController::class, 'resend'])->middleware('auth')->name('verification.resend');

    // 👤 Perfil cliente autenticado
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/my-reservations', [BookingController::class, 'myReservations'])->name('my-reservations');
        Route::get('/my-reservations/{booking}/receipt', [BookingController::class, 'showReceipt'])->name('my-reservations.receipt');
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

            // Fechas excluidas
            Route::resource('excluded_dates', TourExcludedDateController::class)->except(['show']);
        });

        // Reservaciones
        Route::get('reservas/pdf', [BookingController::class, 'generarPDF'])->name('reservas.pdf');
        Route::get('reservas/{reserva}/comprobante', [BookingController::class, 'generarComprobante'])->name('reservas.comprobante');
        Route::resource('reservas', BookingController::class)->except(['show']);
        Route::get('reservas/reserved', [BookingController::class, 'reservedCount'])->name('reservas.reserved');
        Route::get('reservas/calendar-data', [BookingController::class, 'calendarData'])->name('reservas.calendarData');
        Route::get('reservas/calendar', [BookingController::class, 'calendar'])->name('reservas.calendar');

        // Usuarios y roles
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

        // Hoteles
        Route::resource('hotels', HotelListController::class)->except(['show', 'create', 'edit']);
        Route::resource('amenities', AmenityController::class); // Doble, si se usa en otro contexto

        // Carrito
        Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
        Route::post('/carrito', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/carrito/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::post('/carrito/item/{item}/update', [CartController::class, 'updateFromPost'])->name('cart.updateFromPost');
        Route::delete('/carrito/item/{item}', [CartController::class, 'destroy'])->name('cart.item.destroy');

        Route::get('/carritos-todos', [CartController::class, 'allCarts'])->name('cart.general');
        Route::delete('/admin/carrito/item/{item}', [CartController::class, 'destroy'])->name('admin.cart.item.destroy');

        // Confirmar reservas desde carrito
        Route::post('/reservas/from-cart', [BookingController::class, 'storeFromCart'])->name('reservas.storeFromCart');
    });
});
