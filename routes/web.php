<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Middleware\SetLocale;

// Públicos & Panel (no-auth)
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\PublicEmailVerificationController;

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
use App\Http\Controllers\Admin\MeetingPointSimpleController;
use App\Http\Controllers\Reviews\ReviewsController;
use App\Http\Controllers\Admin\Reviews\ReviewAdminController;
use App\Http\Controllers\Admin\Reviews\ReviewProviderController;
use App\Http\Controllers\Admin\Reviews\ReviewReplyController;
use App\Http\Controllers\Admin\Reviews\ReviewRequestAdminController;
use App\Http\Controllers\Reviews\PublicReviewController;
use App\Http\Controllers\Auth\UnlockAccountController;

// Helper para rutas localizadas
if (!function_exists('localizedRoutes')) {
    function localizedRoutes(Closure $callback) {
        $locales = array_keys(config('routes.locales', ['es' => []]));

        foreach ($locales as $locale) {
            Route::prefix($locale)
                ->name("{$locale}.")
                ->group($callback);
        }
    }
}

// ============================================
// REDIRECCIÓN RAÍZ
// ============================================
Route::get('/', function () {
    $locale = session('locale', config('routes.default_locale', 'es'));
    return redirect("/{$locale}");
});

// ============================================
// APLICAR MIDDLEWARE GLOBAL
// ============================================
Route::middleware([SetLocale::class])->group(function () {

    // ============================================
    // CAMBIO DE IDIOMA (sin prefijo)
    // ============================================
    Route::get('/language/{language}', [DashBoardController::class, 'switchLanguage'])
        ->name('switch.language');

    // ============================================
    // RUTAS PÚBLICAS LOCALIZADAS
    // ============================================
    localizedRoutes(function () {
        // Home
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // Tours
        Route::get('/tours', [HomeController::class, 'allTours'])->name('tours.index');
Route::get('/tours/{tour:slug}', [HomeController::class, 'showTour'])->name('tours.show');

        // Contact
        Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
        Route::post('/contact', [HomeController::class, 'sendContact'])
            ->middleware('throttle:6,1')
            ->name('contact.send');

        // FAQ
        Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

        // Políticas
        Route::get('/policies', [\App\Http\Controllers\PoliciesController::class, 'index'])
            ->name('policies.index');
        Route::get('/policies/{policy}', [\App\Http\Controllers\PoliciesController::class, 'show'])
            ->name('policies.show');

        // Reviews (localizadas para traducir nombres de tours)
        Route::get('/reviews', [ReviewsController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/tours/{tour}', [ReviewsController::class, 'tour'])->name('reviews.tour');
    });

    // ============================================
    // REVIEWS - Rutas sin prefijo (funcionales)
    // ============================================
    Route::post('/reviews', [ReviewsController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/embed/{provider}', [ReviewsController::class, 'embed'])
        ->where('provider', '[A-Za-z0-9_-]+')
        ->name('reviews.embed');

    // Review requests públicos (sin prefijo)
    Route::get('/r/{token}', [PublicReviewController::class, 'show'])->name('reviews.request.show');
    Route::post('/r/{token}', [PublicReviewController::class, 'submit'])->name('reviews.request.submit');
    Route::view('/reviews/thanks', 'reviews.thanks')->name('reviews.thanks');

    // ============================================
    // CARRITO & PROMO (sin prefijo)
    // ============================================
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count.public');
    Route::post('/api/apply-promo', [PromoCodeController::class, 'apply'])->name('api.promo.apply');
    Route::post('/apply-promo', [PromoCodeController::class, 'apply'])->name('promo.apply');

    // ============================================
    // AUTH & VERIFICACIÓN (sin prefijo)
    // ============================================
    Route::view('/account/locked', 'auth.account-locked')->name('account.locked');
    Route::get('/auth/throttled', fn () => response()->view('errors.429'))->name('auth.throttled');

    Route::get('/unlock-account', [UnlockAccountController::class, 'form'])->name('unlock.form');
    Route::post('/unlock-account', [UnlockAccountController::class, 'send'])
        ->middleware('throttle:3,1')
        ->name('unlock.send');
    Route::get('/unlock-account/{user}/{hash}', [UnlockAccountController::class, 'process'])
        ->middleware('signed')
        ->name('unlock.process');

    // Verificación de email pública
    Route::get('/email/verify/public/{id}/{hash}', PublicEmailVerificationController::class)
        ->middleware(['signed','throttle:6,1'])
        ->name('verification.public');

    Route::post('/email/verify/public/resend', function (\Illuminate\Http\Request $request) {
        $request->validate(['email' => ['required','email']]);

        $u = \App\Models\User::where('email', mb_strtolower(trim($request->email)))->first();

        if ($u && $u instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $u->hasVerifiedEmail()) {
            $key = 'verify:mail:'.$u->getKey();
            if (! \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 1)) {
                \Illuminate\Support\Facades\RateLimiter::hit($key, 10 * 60);
                try {
                    $u->sendEmailVerificationNotification();
                } catch (\Throwable $e) {
                    logger()->error('Resend verify fail', ['e'=>$e->getMessage()]);
                }
            }
        }

        return back()->with('status', __('adminlte::auth.verify.resent_link_if_exists'));
    })->middleware('throttle:3,1')->name('verification.public.resend');

    // ============================================
    // TEST EMAIL (dev - sin prefijo)
    // ============================================
    Route::get('/send-test-email', function () {
        $booking = \App\Models\Booking::latest()->with(['user', 'detail', 'tour'])->first();
        if ($booking) {
            Mail::to($booking->user->email)->send(new \App\Mail\BookingCreatedMail($booking));
        }
        return 'Correo enviado!';
    });

    // ============================================
    // PERFIL & CARRITO (Privado - sin prefijo)
    // ============================================
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/my-reservations', [BookingController::class, 'myReservations'])
            ->name('my-reservations');
        Route::get('/my-reservations/{booking}/receipt', [BookingController::class, 'showReceipt'])
            ->name('my-reservations.receipt');

        // Carrito
        Route::get('/my-cart', [CartController::class, 'index'])->name('public.cart.index');
        Route::get('/mi-carrito', [CartController::class, 'index'])->name('public.cart.index.es');

        Route::post('/carrito/agregar/{tour}', [CartController::class, 'store'])
            ->name('carrito.agregar');
        Route::post('/reservas/from-cart', [BookingController::class, 'storeFromCart'])
            ->name('public.reservas.storeFromCart');
        Route::delete('/cart/{item}', [CartController::class, 'destroy'])
            ->name('public.cart.destroy');
        Route::put('/cart/{item}', [CartController::class, 'update'])
            ->name('public.cart.update');
        Route::post('/cart/expire', [CartController::class, 'expire'])
            ->name('public.cart.expire');
        Route::post('/cart/refresh-expiry', [CartController::class, 'refreshExpiry'])
            ->name('public.cart.refreshExpiry');
    });

    // ============================================
    // ADMIN (sin prefijo)
    // ============================================
    Route::middleware(['auth', 'verified', 'can:access-admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Perfil admin
            Route::get('/profile', [ProfileController::class, 'adminShow'])->name('profile.show');
            Route::get('/profile/edit', [ProfileController::class, 'adminEdit'])->name('profile.edit');
            Route::post('/profile/edit', [ProfileController::class, 'adminUpdate'])->name('profile.update');

            // Resto del panel (2FA obligatorio)
            Route::middleware('2fa.admin')->group(function () {

                Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

                // Acciones sensibles de usuario
                Route::patch('users/{user}/lock', [UserRegisterController::class, 'lock'])->name('users.lock');
                Route::patch('users/{user}/unlock', [UserRegisterController::class, 'unlock'])->name('users.unlock');
                Route::patch('users/{user}/mark-verified', [UserRegisterController::class, 'markVerified'])->name('users.markVerified');

                // Traducciones
Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
Route::get('translations/{type}/select', [TranslationController::class, 'select'])->name('translations.select');
Route::get('translations/{type}/{id}/locale', [TranslationController::class, 'selectLocale'])->name('translations.locale');
Route::get('translations/{type}/{id}/edit', [TranslationController::class, 'edit'])->name('translations.edit');
Route::post('translations/{type}/{id}/update', [TranslationController::class, 'update'])->name('translations.update');
Route::post('translations/change-editing-locale', [TranslationController::class, 'changeEditingLocale'])->name('translations.change-editing-locale');

                // FAQs
                Route::resource('faqs', AdminFaqController::class)->except(['show']);
                Route::post('faqs/{faq}/toggle', [AdminFaqController::class, 'toggleStatus'])->name('faqs.toggleStatus');

                // Cut-off (booking)
                Route::prefix('tours')->name('tours.')->group(function () {
                    Route::prefix('cutoff')->name('cutoff.')->group(function () {
                        Route::get('/', [CutOffController::class, 'edit'])->name('edit');
                        Route::put('/', [CutOffController::class, 'update'])->name('update');
                        Route::put('/tour', [CutOffController::class, 'updateTourOverrides'])->name('tour.update');
                        Route::put('/schedule', [CutOffController::class, 'updateScheduleOverrides'])->name('schedule.update');
                    });
                });

                // Selector de imágenes de tours
                Route::get('tours/images', [TourImageController::class, 'pick'])->name('tours.images.pick');
                Route::prefix('tours/{tour}/images')->name('tours.images.')->group(function () {
                    Route::get('/', [TourImageController::class, 'index'])->name('index');
                    Route::post('/', [TourImageController::class, 'store'])->name('store');
                    Route::patch('{image}', [TourImageController::class, 'update'])->name('update');
                    Route::delete('{image}', [TourImageController::class, 'destroy'])->name('destroy');
                    Route::post('{image}/cover', [TourImageController::class, 'setCover'])->name('cover');
                    Route::post('reorder', [TourImageController::class, 'reorder'])->name('reorder');
                });

                // Meeting Points
                Route::resource('meetingpoints', MeetingPointSimpleController::class)->except(['show', 'create', 'edit']);
                Route::patch('meetingpoints/{meetingpoint}/toggle', [MeetingPointSimpleController::class, 'toggle'])->name('meetingpoints.toggle');

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
                Route::get('promoCode', [PromoCodeController::class, 'index'])->name('promoCode.index');
                Route::post('promoCode', [PromoCodeController::class, 'store'])->name('promoCode.store');
                Route::delete('promoCode/{promo}', [PromoCodeController::class, 'destroy'])->name('promoCode.destroy');

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
                Route::get('carritos-todos', [CartController::class, 'allCarts'])->name('cart.general');
                Route::delete('carritos/{cart}', [CartController::class, 'destroyCart'])->name('cart.destroy');
                Route::patch('carritos/{cart}/toggle', [CartController::class, 'toggleActive'])->name('cart.toggle');
                Route::post('carrito/apply-promo', [CartController::class, 'applyPromoAdmin'])->name('cart.applyPromo');
                Route::delete('carrito/remove-promo', [CartController::class, 'removePromoAdmin'])->name('cart.removePromo');

                // REVIEWS (solo roles con manage-reviews)
                Route::middleware(['can:manage-reviews'])->group(function () {
                    // PROVEEDORES
                    Route::resource('review-providers', ReviewProviderController::class)
                        ->except(['show'])
                        ->parameters(['review-providers' => 'provider'])
                        ->names('review-providers');

                    Route::post('review-providers/{provider}/toggle', [ReviewProviderController::class, 'toggle'])->name('review-providers.toggle');
                    Route::post('review-providers/{provider}/test', [ReviewProviderController::class, 'test'])->name('review-providers.test');
                    Route::post('review-providers/{provider}/cache/flush', [ReviewProviderController::class, 'flushCache'])->name('review-providers.flush');

                    // RESEÑAS (moderación + CRUD local)
                    Route::get('reviews', [ReviewAdminController::class, 'index'])->name('reviews.index');
                    Route::get('reviews/create', [ReviewAdminController::class, 'create'])->name('reviews.create');
                    Route::post('reviews', [ReviewAdminController::class, 'store'])->name('reviews.store');
                    Route::get('reviews/{review}/edit', [ReviewAdminController::class, 'edit'])->name('reviews.edit');
                    Route::put('reviews/{review}', [ReviewAdminController::class, 'update'])->name('reviews.update');
                    Route::delete('reviews/{review}', [ReviewAdminController::class, 'destroy'])->name('reviews.destroy');

                    // Acciones de moderación
                    Route::post('reviews/{review}/publish', [ReviewAdminController::class, 'publish'])->name('reviews.publish');
                    Route::post('reviews/{review}/hide', [ReviewAdminController::class, 'hide'])->name('reviews.hide');
                    Route::post('reviews/{review}/flag', [ReviewAdminController::class, 'flag'])->name('reviews.flag');
                    Route::post('reviews/bulk', [ReviewAdminController::class, 'bulk'])->name('reviews.bulk');

                    // Respuestas de admin a reseñas
                    Route::get('reviews/{review}/replies/create', [ReviewReplyController::class, 'create'])->name('reviews.replies.create');
                    Route::post('reviews/{review}/replies', [ReviewReplyController::class, 'store'])->name('reviews.replies.store');
                    Route::delete('reviews/{review}/replies/{reply}', [ReviewReplyController::class, 'destroy'])->name('reviews.replies.destroy');
                    Route::post('reviews/{review}/replies/{reply}/toggle', [ReviewReplyController::class, 'toggle'])->name('reviews.replies.toggle');

                    // Hilo (thread) de una reseña
                    Route::get('reviews/{review}/thread', [ReviewReplyController::class, 'thread'])->name('reviews.replies.thread');

                    // Solicitudes post-compra
                    Route::get('review-requests', [ReviewRequestAdminController::class, 'index'])->name('review-requests.index');
                    Route::post('review-requests/{booking}/send', [ReviewRequestAdminController::class, 'send'])->name('review-requests.send');
                    Route::post('review-requests/{rr}/resend', [ReviewRequestAdminController::class, 'resend'])->name('review-requests.resend');
                    Route::delete('review-requests/{rr}', [ReviewRequestAdminController::class, 'destroy'])->name('review-requests.destroy');
                });
            });
        });
});
