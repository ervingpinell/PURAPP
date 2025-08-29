<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;

// Públicos & Auth
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\UnlockAccountController;
use App\Http\Controllers\Admin\UserVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FaqController;

// Admin
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
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\PolicySectionController;
use App\Http\Controllers\Admin\TourImageController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;

use Illuminate\Support\Facades\Mail;
use App\Models\Tour;

Route::middleware([SetLocale::class])->group(function () {

    /**
     * =======================
     * Públicas
     * =======================
     */
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/language/{language}', [DashBoardController::class, 'switchLanguage'])->name('switch.language');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
    Route::get('/tours', [HomeController::class, 'allTours'])->name('tours.index');
    Route::get('/tour/{id}', [HomeController::class, 'showTour'])->name('tours.show');
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('/contact', [HomeController::class, 'sendContact'])
        ->middleware('throttle:6,1')   // máx 6 envíos por minuto por IP
        ->name('contact.send');


    // Reviews públicas (Viator)
    Route::get('/reviews', function () {
        $tours = Tour::whereNotNull('viator_code')
            ->active()
            ->select('tour_id', 'name', 'viator_code')
            ->with(['translations' => function ($q) {
                $q->select('id', 'tour_id', 'locale', 'name', 'overview');
            }])
            ->get();
        return view('public.reviews', compact('tours'));
    })->name('reviews');

    // Políticas públicas
    Route::get('/politicas', [\App\Http\Controllers\PoliciesController::class, 'index'])
        ->name('policies.index');
    Route::get('/politicas/{policy}', [\App\Http\Controllers\PoliciesController::class, 'show'])
        ->name('policies.show');

    // Test correo (dev)
    Route::get('/send-test-email', function () {
        $booking = App\Models\Booking::latest()->with(['user', 'detail', 'tour'])->first();
        Mail::to($booking->user->email)->send(new App\Mail\BookingCreatedMail($booking));
        return 'Correo enviado!';
    });

    // Contador del carrito (JS público)
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count.public');

    // Promo Codes
    Route::post('/api/apply-promo', [PromoCodeController::class, 'apply'])->name('api.promo.apply');
    Route::post('/apply-promo',     [PromoCodeController::class, 'apply'])->name('promo.apply');

    /**
     * =======================
     * Auth (Login / Register / Email Verify / Password Reset / Unlock)
     * =======================
     */

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::view('/account/locked', 'auth.account-locked')->name('account.locked');

    // Unlock Account
Route::get('/unlock-account/{user}/{hash}', [UnlockAccountController::class, 'process'])
    ->middleware('signed')
    ->name('account.unlock.process');



    // Register
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register.store')
        ->middleware('throttle:3,1');
Route::get('/register/thanks', function () {
    // Si quieres mostrar el correo, lo tomamos de sesión y lo “enmascaramos”
    $email = session('registered_email');
    return view('auth.register-thanks', compact('email'));
})->name('register.thanks');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Email Verification
Route::get('/email/verify', [VerifyEmailController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
    ->middleware(['auth', 'throttle:3,1'])
    ->name('verification.send');


    // Password Reset
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->middleware('guest')
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest', 'throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->middleware('guest')
        ->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.update');

    // Aliases legacy
    Route::get('/password/reset', fn () => redirect()->route('password.request'))
        ->middleware('guest');
    Route::post('/password/email', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest');
    Route::get('/password/reset/{token}', [NewPasswordController::class, 'create'])
        ->middleware('guest');
    Route::post('/password/reset', [NewPasswordController::class, 'store'])
        ->middleware('guest');

    /**
     * =======================
     * Perfil cliente + Carrito
     * =======================
     */
   Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/my-reservations', [BookingController::class, 'myReservations'])->name('my-reservations');
        Route::get('/my-reservations/{booking}/receipt', [BookingController::class, 'showReceipt'])->name('my-reservations.receipt');

        // Carrito
        Route::get('/mi-carrito', [CartController::class, 'index'])->name('public.cart.index');
        Route::post('/carrito/agregar/{tour}', [CartController::class, 'store'])->name('carrito.agregar');
        Route::post('/reservas/from-cart', [BookingController::class, 'storeFromCart'])->name('public.reservas.storeFromCart');
        Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('public.cart.destroy');
        Route::put('/cart/{item}', [CartController::class, 'update'])->name('public.cart.update');
    });

    /**
     * =======================
     * Admin
     * =======================
     */
    Route::middleware(['auth', 'verified', 'CheckRole'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

        // Perfil admin
        Route::get('/profile', [ProfileController::class, 'adminShow'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'adminEdit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'adminUpdate'])->name('profile.update');

        // Traducciones
        Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
        Route::get('translations/{type}/select', [TranslationController::class, 'select'])->name('translations.select');
        Route::get('translations/{type}/{id}/locale', [TranslationController::class, 'selectLocale'])->name('translations.locale');
        Route::get('translations/{type}/{id}/edit', [TranslationController::class, 'edit'])->name('translations.edit');
        Route::post('translations/{type}/{id}/update', [TranslationController::class, 'update'])->name('translations.update');

        // FAQs
        Route::resource('faqs', AdminFaqController::class)->except(['show']);
        Route::post('faqs/{faq}/toggle', [AdminFaqController::class, 'toggleStatus'])->name('faqs.toggleStatus');

        // Imágenes de Tours
        Route::get('tours/images', [TourImageController::class, 'pick'])->name('tours.images.pick');
        Route::prefix('tours/{tour}/images')->name('tours.images.')->group(function () {
            Route::get('/',        [TourImageController::class, 'index'])->name('index');
            Route::post('/',       [TourImageController::class, 'store'])->name('store');
            Route::delete('{img}', [TourImageController::class, 'destroy'])->name('destroy');
            Route::post('reorder', [TourImageController::class, 'reorder'])->name('reorder');
            Route::post('{img}/cover', [TourImageController::class, 'setCover'])->name('cover');
            Route::patch('{img}',  [TourImageController::class, 'update'])->name('update');
        });

        // Policies & Sections
        Route::get('policies', [PolicyController::class, 'index'])->name('policies.index');
        Route::post('policies', [PolicyController::class, 'store'])->name('policies.store');
        Route::put('policies/{policy}', [PolicyController::class, 'update'])->name('policies.update');
        Route::post('policies/{policy}/toggle', [PolicyController::class, 'toggle'])->name('policies.toggle');
        Route::delete('policies/{policy}', [PolicyController::class, 'destroy'])->name('policies.destroy');

        Route::get('policies/{policy}/sections', [PolicySectionController::class, 'index'])->name('policies.sections.index');
        Route::post('policies/{policy}/sections', [PolicySectionController::class, 'store'])->name('policies.sections.store');
        Route::put('policies/{policy}/sections/{section}', [PolicySectionController::class, 'update'])->name('policies.sections.update');
        Route::post('policies/{policy}/sections/{section}/toggle', [PolicySectionController::class, 'toggle'])->name('policies.sections.toggle');
        Route::delete('policies/{policy}/sections/{section}', [PolicySectionController::class, 'destroy'])->name('policies.sections.destroy');

        // Promo Codes
        Route::get('/promoCode', [PromoCodeController::class, 'index'])->name('promoCode.index');
        Route::post('/promoCode', [PromoCodeController::class, 'store'])->name('promoCode.store');
        Route::delete('/promoCode/{promo}', [PromoCodeController::class, 'destroy'])->name('promoCode.destroy');

        // Tours

        Route::resource('tours', TourController::class)
            ->except(['create', 'edit', 'show', 'destroy']);

        Route::patch('tours/{tour:tour_id}/toggle', [TourController::class, 'toggle'])
            ->name('tours.toggle');

        Route::prefix('tours')->name('tours.')->group(function () {
            Route::resource('schedule', TourScheduleController::class)->except(['create','edit','show']);
            Route::put('schedule/{schedule}/toggle', [TourScheduleController::class, 'toggle'])->name('schedule.toggle');
            Route::post('schedule/{tour}/attach', [TourScheduleController::class, 'attach'])->name('schedule.attach');
            Route::delete('schedule/{tour}/{schedule}/detach', [TourScheduleController::class, 'detach'])->name('schedule.detach');
            Route::patch('schedule/{tour}/{schedule}/assignment-toggle', [TourScheduleController::class, 'toggleAssignment'])->name('schedule.assignment.toggle');

            Route::resource('itinerary', ItineraryController::class)->except(['show']);
            Route::post('itinerary/{itinerary}/assign-items', [ItineraryController::class, 'assignItems'])->name('itinerary.assignItems');
            Route::resource('itinerary_items', ItineraryItemController::class)->except(['show', 'create', 'edit']);
            Route::patch('itinerary_items/{itinerary_item}/toggle', [ItineraryItemController::class, 'toggle'])
            ->name('itinerary_items.toggle');

            Route::resource('availability', TourAvailabilityController::class)->except(['show']);

            Route::get('excluded_dates', [TourExcludedDateController::class, 'index'])->name('excluded_dates.index');
            Route::resource('excluded_dates', TourExcludedDateController::class)->except(['show','index']);
            Route::post('excluded_dates/toggle', [TourExcludedDateController::class, 'toggle'])->name('excluded_dates.toggle');
            Route::post('excluded_dates/bulk-toggle', [TourExcludedDateController::class, 'bulkToggle'])->name('excluded_dates.bulkToggle');
            Route::post('excluded_dates/block-all', [TourExcludedDateController::class, 'blockAll'])->name('excluded_dates.blockAll');
            Route::get('excluded_dates/blocked', [TourExcludedDateController::class, 'blocked'])->name('excluded_dates.blocked');

            Route::resource('amenities', AmenityController::class)->except(['show']);
            Route::patch('amenities/{amenity}/toggle', [AmenityController::class, 'toggle'])->name('amenities.toggle');
        });

        // Reservas
        Route::get('/reservas/excel', [BookingController::class, 'generarExcel'])->name('reservas.excel');
        Route::get('reservas/pdf', [BookingController::class, 'generarPDF'])->name('reservas.pdf');
        Route::get('reservas/{reserva}/comprobante', [BookingController::class, 'generarComprobante'])->name('reservas.comprobante');
        Route::resource('reservas', BookingController::class)->except(['show']);
        Route::get('reservas/reserved', [BookingController::class, 'reservedCount'])->name('reservas.reserved');
        Route::get('reservas/calendar-data', [BookingController::class, 'calendarData'])->name('reservas.calendarData');
        Route::get('reservas/calendar', [BookingController::class, 'calendar'])->name('reservas.calendar');
        Route::post('/reservas/from-cart', [BookingController::class, 'storeFromCart'])->name('reservas.storeFromCart');

        // Usuarios y roles
        Route::resource('users', UserRegisterController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show', 'create']);
        Route::patch('roles/{role}/toggle', [RoleController::class, 'toggle'])->name('roles.toggle');

        // Desbloqueo de usuarios (admin)
        Route::patch('/users/{id}/unlock', [UserRegisterController::class, 'unlock'])->name('users.unlock');

        // Reenviar verificación de email (admin)
        Route::post('/users/{user}/resend-verification', [UserVerificationController::class, 'resend'])
            ->name('users.resendVerification');

        // Tour Types
        Route::resource('tourtypes', TourTypeController::class, ['parameters' => ['tourtypes' => 'tourType']])->except(['show']);
        Route::put('tourtypes/{tourType}/toggle', [TourTypeController::class, 'toggle'])->name('tourtypes.toggle');

        // Idiomas
        Route::resource('languages', TourLanguageController::class, ['parameters' => ['languages' => 'language']])->except(['show']);
        Route::patch('languages/{language}/toggle', [TourLanguageController::class, 'toggle'])->name('languages.toggle');

        // Hoteles
        Route::resource('hotels', HotelListController::class)->except(['show', 'create', 'edit']);
        Route::post('hotels/sort', [HotelListController::class, 'sort'])->name('hotels.sort');
        Route::patch('/hotels/{hotel}/toggle', [HotelListController::class, 'toggle'])->name('hotels.toggle');

        // Carrito Admin
        Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
        Route::post('/carrito', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/carrito/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::post('/carrito/item/{item}/update', [CartController::class, 'updateFromPost'])->name('cart.updateFromPost');
        Route::delete('/carrito/item/{item}', [CartController::class, 'destroy'])->name('cart.item.destroy');
        Route::delete('/admin/carrito/item/{item}', [CartController::class, 'destroy'])->name('admin.cart.item.destroy');
        Route::get('/carritos-todos', [CartController::class, 'allCarts'])->name('cart.general');
        Route::delete('/carritos/{cart}', [CartController::class, 'destroyCart'])->name('cart.destroy');
        Route::patch('/admin/carritos/{cart}/toggle', [CartController::class, 'toggleActive'])->name('cart.toggle');
        Route::post('/carrito/apply-promo',  [CartController::class, 'applyPromoAdmin'])->name('cart.applyPromo');
        Route::delete('/carrito/remove-promo', [CartController::class, 'removePromoAdmin'])->name('cart.removePromo');

    });
});
