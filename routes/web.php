<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Middleware\SetLocale;

// Públicos & Panel (no-auth)
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ProfileController;

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
use App\Http\Controllers\Admin\Tours\CutOffController;
use App\Http\Controllers\Admin\Tours\TourTypeCoverPickerController;
use App\Http\Controllers\Admin\Bookings\BookingController;
use App\Http\Controllers\Admin\Bookings\HotelListController;
use App\Http\Controllers\Admin\Cart\CartController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\PolicySectionController;
use App\Http\Controllers\Admin\TourImageController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Admin\UserVerificationController;

// Models
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
            }])->get();

        return view('public.reviews', compact('tours'));
    })->name('reviews');

    // Políticas públicas
    Route::get('/politicas', [\App\Http\Controllers\PoliciesController::class, 'index'])
        ->name('policies.index');
    Route::get('/politicas/{policy}', [\App\Http\Controllers\PoliciesController::class, 'show'])
        ->name('policies.show');

    // Test correo (dev)
    Route::get('/send-test-email', function () {
        $booking = \App\Models\Booking::latest()->with(['user', 'detail', 'tour'])->first();
        if ($booking) {
            Mail::to($booking->user->email)->send(new \App\Mail\BookingCreatedMail($booking));
        }
        return 'Correo enviado!';
    });

    // Contador del carrito (JS público)
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count.public');

    // Promo Codes (público o semi-público)
    Route::post('/api/apply-promo', [PromoCodeController::class, 'apply'])->name('api.promo.apply');
    Route::post('/apply-promo',     [PromoCodeController::class, 'apply'])->name('promo.apply');

    /**
     * =======================
     * Auth (Fortify)
     * =======================
     *
     * Fortify registra automáticamente:
     * - GET /login, POST /login, POST /logout
     * - GET /register, POST /register
     * - GET /forgot-password, POST /forgot-password
     * - GET /reset-password/{token}, POST /reset-password
     * - GET /email/verify, GET /email/verify/{id}/{hash}, POST /email/verification-notification
     * - GET /two-factor-challenge, POST /two-factor-challenge (si 2FA activo)
     *
     * No definas rutas duplicadas para evitar conflictos.
     */
    Route::view('/account/locked', 'auth.account-locked')->name('account.locked'); // opcional

    /**
     * =======================
     * Perfil cliente + Carrito (Privado)
     * =======================
     */
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/my-reservations', [BookingController::class, 'myReservations'])->name('my-reservations');
        Route::get('/my-reservations/{booking}/receipt', [BookingController::class, 'showReceipt'])->name('my-reservations.receipt');

        // Carrito (público/privado según tu flujo)
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
     *
     * - Requiere auth + verified + CheckRole (1,2)
     * - El perfil admin (show/edit/update) queda FUERA del '2fa.admin' para poder activarlo sin bucles
     * - El resto del panel va dentro de '2fa.admin'
     */
    Route::middleware(['auth', 'verified', 'CheckRole'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // ---- Perfil admin (SIN 2FA obligatorio: se usa para activar 2FA) ----
            Route::get('/profile',      [ProfileController::class, 'adminShow'])->name('profile.show');
            Route::get('/profile/edit', [ProfileController::class, 'adminEdit'])->name('profile.edit');
            Route::post('/profile/edit',[ProfileController::class, 'adminUpdate'])->name('profile.update');

            // ---- Resto del panel (2FA obligatorio) ----
            Route::middleware('2fa.admin')->group(function () {

                Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

                // Perfil admin (si quieres repetir show con 2FA ya activo, puedes dejar solo el de arriba)
                // Route::get('/profile', [ProfileController::class, 'adminShow'])->name('profile.show');

                // Traducciones
                Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
                Route::get('translations/{type}/select', [TranslationController::class, 'select'])->name('translations.select');
                Route::get('translations/{type}/{id}/locale', [TranslationController::class, 'selectLocale'])->name('translations.locale');
                Route::get('translations/{type}/{id}/edit', [TranslationController::class, 'edit'])->name('translations.edit');
                Route::post('translations/{type}/{id}/update', [TranslationController::class, 'update'])->name('translations.update');

                // FAQs
                Route::resource('faqs', AdminFaqController::class)->except(['show']);
                Route::post('faqs/{faq}/toggle', [AdminFaqController::class, 'toggleStatus'])->name('faqs.toggleStatus');

                // Cut-off (booking)
                Route::prefix('tours')->name('tours.')->group(function () {
                    Route::prefix('cutoff')->name('cutoff.')->group(function () {
                        Route::get('/',         [CutOffController::class, 'edit'])->name('edit');
                        Route::put('/',         [CutOffController::class, 'update'])->name('update');
                        Route::put('/tour',     [CutOffController::class, 'updateTourOverrides'])->name('tour.update');
                        Route::put('/schedule', [CutOffController::class, 'updateScheduleOverrides'])->name('schedule.update');
                    });
                });

                // Selector de imágenes de tours
                Route::get('tours/images', [TourImageController::class, 'pick'])->name('tours.images.pick');
                Route::prefix('tours/{tour}/images')->name('tours.images.')->group(function () {
                    Route::get('/',              [TourImageController::class, 'index'])->name('index');
                    Route::post('/',             [TourImageController::class, 'store'])->name('store');
                    Route::patch('{image}',      [TourImageController::class, 'update'])->name('update');
                    Route::delete('{image}',     [TourImageController::class, 'destroy'])->name('destroy');
                    Route::post('{image}/cover', [TourImageController::class, 'setCover'])->name('cover');
                    Route::post('reorder',       [TourImageController::class, 'reorder'])->name('reorder');
                });

                // Tour Type covers
                Route::prefix('types')->name('types.')->group(function () {
                    Route::get('images', [TourTypeCoverPickerController::class, 'pick'])->name('images.pick');
                    Route::get('images/{tourType}/edit', [TourTypeCoverPickerController::class, 'edit'])->name('images.edit');
                    Route::put('images/{tourType}', [TourTypeCoverPickerController::class, 'updateCover'])->name('images.update');
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
                Route::resource('tours', TourController::class)->except(['create', 'edit', 'show', 'destroy']);
                Route::patch('tours/{tour:tour_id}/toggle', [TourController::class, 'toggle'])->name('tours.toggle');

                Route::prefix('tours')->name('tours.')->group(function () {
                    Route::resource('schedule', TourScheduleController::class)->except(['create','edit','show']);
                    Route::put('schedule/{schedule}/toggle', [TourScheduleController::class, 'toggle'])->name('schedule.toggle');
                    Route::post('schedule/{tour}/attach', [TourScheduleController::class, 'attach'])->name('schedule.attach');
                    Route::delete('schedule/{tour}/{schedule}/detach', [TourScheduleController::class, 'detach'])->name('schedule.detach');
                    Route::patch('schedule/{tour}/{schedule}/assignment-toggle', [TourScheduleController::class, 'toggleAssignment'])->name('schedule.assignment.toggle');

                    Route::resource('itinerary', ItineraryController::class)->except(['show']);
                    Route::patch('itineraries/{itinerary}/toggle', [ItineraryController::class, 'toggle'])->name('itinerary.toggle');
                    Route::post('itinerary/{itinerary}/assign-items', [ItineraryController::class, 'assignItems'])->name('itinerary.assignItems');

                    Route::resource('itinerary_items', ItineraryItemController::class)->except(['show', 'create', 'edit']);
                    Route::patch('itinerary_items/{itinerary_item}/toggle', [ItineraryItemController::class, 'toggle'])->name('itinerary_items.toggle');

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
                Route::get('reservas/excel', [BookingController::class, 'generarExcel'])->name('reservas.excel');
                Route::get('reservas/pdf', [BookingController::class, 'generarPDF'])->name('reservas.pdf');
                Route::get('reservas/{reserva}/comprobante', [BookingController::class, 'generarComprobante'])->name('reservas.comprobante');
                Route::resource('reservas', BookingController::class)->except(['show']);
                Route::get('reservas/reserved', [BookingController::class, 'reservedCount'])->name('reservas.reserved');
                Route::get('reservas/calendar-data', [BookingController::class, 'calendarData'])->name('reservas.calendarData');
                Route::get('reservas/calendar', [BookingController::class, 'calendar'])->name('reservas.calendar');
                Route::post('reservas/from-cart', [BookingController::class, 'storeFromCart'])->name('reservas.storeFromCart');

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
                Route::patch('hotels/{hotel}/toggle', [HotelListController::class, 'toggle'])->name('hotels.toggle');

                // Carrito Admin
                Route::get('carrito', [CartController::class, 'index'])->name('cart.index');
                Route::post('carrito', [CartController::class, 'store'])->name('cart.store');
                Route::patch('carrito/{item}', [CartController::class, 'update'])->name('cart.update');
                Route::post('carrito/item/{item}/update', [CartController::class, 'updateFromPost'])->name('cart.updateFromPost');
                Route::delete('carrito/item/{item}', [CartController::class, 'destroy'])->name('cart.item.destroy');
                Route::delete('carritos/{cart}', [CartController::class, 'destroyCart'])->name('cart.destroy');
                Route::get('carritos-todos', [CartController::class, 'allCarts'])->name('cart.general');
                Route::patch('carritos/{cart}/toggle', [CartController::class, 'toggleActive'])->name('cart.toggle');
                Route::post('carrito/apply-promo',  [CartController::class, 'applyPromoAdmin'])->name('cart.applyPromo');
                Route::delete('carrito/remove-promo', [CartController::class, 'removePromoAdmin'])->name('cart.removePromo');
            });
        });
});
