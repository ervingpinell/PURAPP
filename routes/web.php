<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;

// Controllers públicos y de autenticación
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ClienteRegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FaqController;

// Controladores del panel admin
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
use App\Http\Controllers\Admin\Tours\TourExcludedDateController;
use App\Http\Controllers\Admin\Bookings\BookingController;
use App\Http\Controllers\Admin\Bookings\HotelListController;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\TranslationController;

// Otros
use App\Mail\TestEmail;
use App\Models\Tour;
use Illuminate\Support\Facades\Mail;

Route::middleware([SetLocale::class])->group(function () {

    // 🔧 Test de traducciones
    Route::get('/test-translations/{tour}', function (Tour $tour) {
        return $tour->translations->mapWithKeys(fn($t) => [$t->locale => [
            'name' => $t->name,
            'overview' => $t->overview
        ]]);
    });

    // 🌐 Rutas públicas
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/language/{language}', [DashBoardController::class, 'switchLanguage'])->name('switch.language');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
    Route::get('/tour/{id}', [HomeController::class, 'showTour'])->name('tours.show');
    Route::get('/tours', [HomeController::class, 'allTours'])->name('tours.index');
Route::get('/cart/count', [CartController::class, 'count'])->name('public.cart.count');

    // 📧 Test de correo
    Route::get('/send-test-email', function () {
        $booking = App\Models\Booking::latest()->with(['user','detail','tour'])->first();
        Mail::to($booking->user->email)->send(new App\Mail\BookingCreatedMail($booking));
        return 'Correo enviado!';
    });

    // 🛒 Carrito de compras
    Route::middleware(['auth'])->group(function () {
        Route::get('/mi-carrito', [CartController::class, 'index'])->name('public.cart.index');
    });
    Route::post('/carrito/agregar/{tour}', [CartController::class, 'store'])->middleware('auth')->name('carrito.agregar');
    Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('public.cart.destroy');
    Route::post('/reservas/from-cart', [BookingController::class, 'storeFromCart'])->middleware('auth')->name('public.reservas.storeFromCart');

    // 🛑 Fechas excluidas
    Route::post('/tour-excluded/block-all', [TourExcludedDateController::class, 'storeMultiple'])->name('admin.tour-excluded.store-multiple');
    Route::post('/tour-excluded/block-all-all', [TourExcludedDateController::class, 'blockAll'])->name('admin.tour-excluded.block-all');

    // 🔐 Autenticación
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [ClienteRegisterController::class, 'create'])->name('register');
    Route::post('/register', [ClienteRegisterController::class, 'store'])->name('register.store');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // 📧 Verificación de correo
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->middleware('auth')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/email/resend', [VerifyEmailController::class, 'resend'])->middleware('auth')->name('verification.resend');

    // 👤 Perfil del cliente autenticado
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/my-reservations', [BookingController::class, 'myReservations'])->name('my-reservations');
        Route::get('/my-reservations/{booking}/receipt', [BookingController::class, 'showReceipt'])->name('my-reservations.receipt');
    });

    // 🛠 Panel administrativo
    Route::middleware(['auth', 'CheckRole'])->prefix('admin')->name('admin.')->group(function () {

        // 📊 Dashboard
        Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

        // 👤 Perfil del administrador
        Route::get('/profile', [ProfileController::class, 'adminShow'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'adminEdit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'adminUpdate'])->name('profile.update');

        // 🌍 Traducciones
Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::get('translations/{type}/select', [TranslationController::class, 'select'])->name('translations.select');
    Route::get('translations/{type}/{id}/locale', [TranslationController::class, 'selectLocale'])->name('translations.locale');
    Route::get('translations/{type}/{id}/edit', [TranslationController::class, 'edit'])->name('translations.edit');
    Route::post('translations/{type}/{id}/update', [TranslationController::class, 'update'])->name('translations.update');

        // ❓ FAQ
        Route::resource('faqs', AdminFaqController::class)->except(['show']);
        Route::post('faqs/{faq}/toggle', [AdminFaqController::class, 'toggleStatus'])->name('faqs.toggleStatus');

        // 🎒 Módulo Tours
        Route::resource('tours', TourController::class)->except(['create', 'edit', 'show']);
        Route::prefix('tours')->name('tours.')->group(function () {
            Route::resource('schedule', TourScheduleController::class)->except(['create', 'edit', 'show']);
            Route::put('schedule/{schedule}/toggle', [TourScheduleController::class, 'toggle'])->name('schedule.toggle');
            Route::resource('itinerary', ItineraryController::class)->except(['show']);
            Route::post('itinerary/{itinerary}/assign-items', [ItineraryController::class, 'assignItems'])->name('itinerary.assignItems');
            Route::resource('itinerary_items', ItineraryItemController::class)->except(['show', 'create', 'edit']);
            Route::resource('availability', TourAvailabilityController::class)->except(['show']);
            Route::resource('excluded_dates', TourExcludedDateController::class)->except(['show']);
            Route::post('excluded_dates/block-all', [TourExcludedDateController::class, 'blockAll'])->name('excluded_dates.blockAll');
            Route::resource('amenities', AmenityController::class)->except(['show']);
        });

        // 📆 Reservaciones
        Route::get('reservas/pdf', [BookingController::class, 'generarPDF'])->name('reservas.pdf');
        Route::get('reservas/{reserva}/comprobante', [BookingController::class, 'generarComprobante'])->name('reservas.comprobante');
        Route::resource('reservas', BookingController::class)->except(['show']);
        Route::get('reservas/reserved', [BookingController::class, 'reservedCount'])->name('reservas.reserved');
        Route::get('reservas/calendar-data', [BookingController::class, 'calendarData'])->name('reservas.calendarData');
        Route::get('reservas/calendar', [BookingController::class, 'calendar'])->name('reservas.calendar');

        // 👥 Usuarios y roles
        Route::resource('users', UserRegisterController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);

        // 📚 Categorías, idiomas y tipos de tour
        Route::resource('tourtypes', TourTypeController::class, ['parameters' => ['tourtypes' => 'tourType']])->except(['show']);
        Route::put('tourtypes/{tourType}/toggle', [TourTypeController::class, 'toggle'])->name('tourtypes.toggle');
        Route::resource('languages', TourLanguageController::class, ['parameters' => ['languages' => 'language']])->except(['show']);

        // 🏨 Hoteles
        Route::resource('hotels', HotelListController::class)->except(['show', 'create', 'edit']);

        // 🛒 Carrito de administración
        Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
        Route::post('/carrito', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/carrito/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::post('/carrito/item/{item}/update', [CartController::class, 'updateFromPost'])->name('cart.updateFromPost');
        Route::delete('/carrito/item/{item}', [CartController::class, 'destroy'])->name('cart.item.destroy');
        Route::get('/carritos-todos', [CartController::class, 'allCarts'])->name('cart.general');
        Route::delete('/admin/carrito/item/{item}', [CartController::class, 'destroy'])->name('admin.cart.item.destroy');
        Route::post('/reservas/from-cart', [BookingController::class, 'storeFromCart'])->name('reservas.storeFromCart');
    });
});
