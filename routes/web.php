<?php

use App\Http\Controllers\Auth\PublicEmailVerificationController;
use App\Http\Controllers\Auth\UnlockAccountController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PoliciesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reviews\PublicReviewController;
use App\Http\Controllers\Reviews\ReviewsController;
use App\Http\Controllers\SitemapController;

// Public (auth) cart controller
use App\Http\Controllers\CartController as PublicCartController;

// Admin controllers
use App\Http\Controllers\Admin\Bookings\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\Bookings\HotelListController;
use App\Http\Controllers\Admin\Cart\CartController as AdminCartController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\Languages\TourLanguageController;
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\PolicySectionController;
use App\Http\Controllers\Admin\PromoCode\PromoCodeController;
use App\Http\Controllers\Admin\Reports\ReportsController;
use App\Http\Controllers\Admin\Reviews\ReviewAdminController;
use App\Http\Controllers\Admin\Reviews\ReviewProviderController;
use App\Http\Controllers\Admin\Reviews\ReviewReplyController;
use App\Http\Controllers\Admin\Reviews\ReviewRequestAdminController;
use App\Http\Controllers\Admin\TourImageController;
use App\Http\Controllers\Admin\Tours\AmenityController;
use App\Http\Controllers\Admin\Tours\CutOffController;
use App\Http\Controllers\Admin\Tours\ItineraryController;
use App\Http\Controllers\Admin\Tours\ItineraryItemController;
use App\Http\Controllers\Admin\Tours\TourAvailabilityController;
use App\Http\Controllers\Admin\Tours\TourController;
use App\Http\Controllers\Admin\Tours\TourExcludedDateController;
use App\Http\Controllers\Admin\Tours\TourScheduleController;
use App\Http\Controllers\Admin\Tours\TourTypeController;
use App\Http\Controllers\Admin\Tours\TourTypeCoverPickerController;
use App\Http\Controllers\Admin\Tours\TourOrderController;
use App\Http\Controllers\Admin\Tours\TourPriceController;
use App\Http\Controllers\Admin\Users\RoleController;
use App\Http\Controllers\Admin\Users\UserRegisterController;
use App\Http\Controllers\Admin\MeetingPointSimpleController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\CapacityController;
use App\Http\Controllers\Admin\CustomerCategoryController;
use App\Http\Controllers\Admin\Tours\TourAjaxController;
use App\Http\Controllers\Admin\API\TourDataController;
use App\Http\Controllers\Auth\EmailChangeController;

// Public bookings controller (split)
use App\Http\Controllers\Bookings\BookingController as PublicBookingController;
use App\Http\Controllers\PublicCheckoutController;

use App\Http\Middleware\SetLocale;
use App\Services\Reviews\ReviewAggregator;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Mail\{
    BookingCreatedMail,
    BookingConfirmedMail,
    BookingCancelledMail,
    BookingUpdatedMail
};

if (app()->isLocal()) {
    Route::prefix('preview/mails/bookings')->group(function () {
        // Helper: toma ultimo booking si no se pasa id
        $resolveBooking = function (?int $id = null): Booking {
            return $id
                ? Booking::with(['detail', 'user', 'tour'])->findOrFail($id)
                : Booking::with(['detail', 'user', 'tour'])->latest('booking_id')->firstOrFail();
        };

        // Booking Created
        Route::get('/{id?}/created', function (?int $id = null) use ($resolveBooking) {
            $booking = $resolveBooking($id);
            $mailable = new BookingCreatedMail($booking);
            if ($lang = request('lang')) {
                $mailable->locale($lang);
            }
            return $mailable->render(); // HTML
        })->name('preview.mail.booking.created');

        // Booking Confirmed
        Route::get('/{id?}/confirmed', function (?int $id = null) use ($resolveBooking) {
            $booking = $resolveBooking($id);
            $mailable = new BookingConfirmedMail($booking);
            if ($lang = request('lang')) {
                $mailable->locale($lang);
            }
            return $mailable->render();
        })->name('preview.mail.booking.confirmed');

        // Booking Cancelled
        Route::get('/{id?}/cancelled', function (?int $id = null) use ($resolveBooking) {
            $booking = $resolveBooking($id);
            $mailable = new BookingCancelledMail($booking);
            if ($lang = request('lang')) {
                $mailable->locale($lang);
            }
            return $mailable->render();
        })->name('preview.mail.booking.cancelled');

        // Booking Updated
        Route::get('/{id?}/updated', function (?int $id = null) use ($resolveBooking) {
            $booking = $resolveBooking($id);
            $mailable = new BookingUpdatedMail($booking);
            if ($lang = request('lang')) {
                $mailable->locale($lang);
            }
            return $mailable->render();
        })->name('preview.mail.booking.updated');
    });
}

/*
|--------------------------------------------------------------------------
| Dynamic robots.txt
|--------------------------------------------------------------------------
*/
Route::get('/robots.txt', function (): Response {
    $lines = [
        'User-agent: *',
        'Disallow: /admin/',
        'Disallow: /storage',
        'Disallow: /telescope',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /password/',
        'Disallow: /cart/',
        'Disallow: /bookings/',
        'Disallow: /user/',
        'Disallow: /api/',
        'Disallow: /reviews/embed/',
        'Allow: /',
        'Sitemap: ' . rtrim(config('app.url'), '/') . '/sitemap.xml',
    ];

    return response(implode("\n", $lines), 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

/*
|--------------------------------------------------------------------------
| Sitemap
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| Localized routes helper
|--------------------------------------------------------------------------
*/
if (!function_exists('localizedRoutes')) {
    function localizedRoutes(\Closure $callback) {
        $locales = array_keys(config('routes.locales', ['es' => []]));
        foreach ($locales as $locale) {
            Route::prefix($locale)
                ->name("{$locale}.")
                ->group($callback);
        }
    }
}

/*
|--------------------------------------------------------------------------
| Root -> locale redirect
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $locale = session('locale', config('routes.default_locale', 'es'));
    return redirect("/{$locale}");
});

/*
|--------------------------------------------------------------------------
| 🔓 Cart count público (para evitar 401 en frontend)
|--------------------------------------------------------------------------
*/
Route::get('/cart/count', [PublicCartController::class, 'count'])
    ->name('cart.count')
    ->middleware('throttle:30,1');

/*
|--------------------------------------------------------------------------
| Localized group
|--------------------------------------------------------------------------
*/
Route::middleware([SetLocale::class])->group(function () {

    // Language switch
    Route::get('/language/{language}', [DashBoardController::class, 'switchLanguage'])
        ->name('switch.language');

    // ============================
    // PUBLIC LOCALIZED ROUTES
    // ============================

    localizedRoutes(function () {
        // ============================
        // Home & Tours
        // ============================
        // Ruta principal: /{locale}  -> es.home, en.home, etc.
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // Alias opcional /{locale}/home, SIN name duplicado
        Route::get('/home', [HomeController::class, 'index']);

        Route::get('/tours', [HomeController::class, 'allTours'])->name('tours.index');
        Route::get('/tours/{tour:slug}', [HomeController::class, 'showTour'])->name('tours.show');

        // ============================
        // Contact
        // ============================
        Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
        Route::post('/contact', [HomeController::class, 'sendContact'])
            ->middleware('throttle:6,1')
            ->name('contact.send');

        // ============================
        // FAQ
        // ============================
        Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

        // ============================
        // Policies
        // ============================
        Route::get('/policies', [PoliciesController::class, 'index'])->name('policies.index');
        Route::get('/policies/{policy:slug}', [PoliciesController::class, 'show'])->name('policies.show');
        Route::get('/policies/id/{policy:policy_id}', [PoliciesController::class, 'showById'])
            ->name('policies.show.id');

        // ============================
        // Public reviews
        // ============================
        Route::get('/reviews', [ReviewsController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/tours/{tour:slug}', [ReviewsController::class, 'tour'])->name('reviews.tour');
    });

    // ============================
    // REVIEWS (no prefix)
    // ============================
    Route::post('/reviews', [ReviewsController::class, 'store'])->name('reviews.store');

    // Embed with NOINDEX
    Route::get('/reviews/embed/{provider}', function (Request $request, ReviewAggregator $agg, string $provider) {
        $response = app(ReviewsController::class)->embed($request, $agg, $provider);
        return $response->header('X-Robots-Tag', 'noindex, nofollow');
    })->where('provider', '[A-Za-z0-9_-]+')->name('reviews.embed');

    Route::get('/r/{token}', [PublicReviewController::class, 'show'])->name('reviews.request.show');
    Route::post('/r/{token}', [PublicReviewController::class, 'submit'])->name('reviews.request.submit');
    Route::view('/reviews/thanks', 'reviews.thanks')->name('reviews.thanks');

    // ============================
    // AUTH & VERIFICATION
    // ============================

    // Account locked
    Route::view('/account/locked', 'auth.account-locked')->name('account.locked');
    Route::get('/auth/throttled', fn () => response()->view('errors.429'))->name('auth.throttled');

    // Unlock account
    Route::get('/unlock-account', [UnlockAccountController::class, 'form'])->name('unlock.form');
    Route::post('/unlock-account', [UnlockAccountController::class, 'send'])
        ->middleware('throttle:3,1')
        ->name('unlock.send');
    Route::get('/unlock-account/{user}/{hash}', [UnlockAccountController::class, 'process'])
        ->middleware('signed')
        ->name('unlock.process');

    // Email verification - Public verification URL (from email)
    Route::get('/email/verify/public/{id}/{hash}', PublicEmailVerificationController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.public');

    // 🆕 Email verification - Notice screen (after registration)
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('guest')->name('verification.notice');

    Route::get('/email/change/confirm/{user}/{token}', [EmailChangeController::class, 'confirm'])
    ->middleware('signed') // si quieres firma
    ->name('email.change.confirm');

    // Email verification - Resend link
    Route::post('/email/verify/public/resend', function (Request $request) {
        $request->validate(['email' => ['required', 'email']]);

        $u = \App\Models\User::where('email', mb_strtolower(trim($request->email)))->first();

        if ($u && $u instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $u->hasVerifiedEmail()) {
            $key = 'verify:mail:' . $u->getKey();
            if (! \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 1)) {
                \Illuminate\Support\Facades\RateLimiter::hit($key, 10 * 60);
                try {
                    $u->sendEmailVerificationNotification();
                } catch (\Throwable $e) {
                    logger()->error('Resend verify fail', ['e' => $e->getMessage()]);
                }
            }
        }

        return back()->with('status', __('auth.verify.resent'));
    })->middleware('throttle:3,1')->name('verification.public.resend');

    // ------------------------------
    // Profile & cart (private) — READONLY-BLOCKABLE
    // ------------------------------
    Route::middleware(['auth', 'verified'])->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

        // My bookings (public controller)
        Route::get('/my-bookings', [PublicBookingController::class, 'myBookings'])->name('my-bookings');
        Route::get('/my-bookings/{booking}/receipt', [PublicBookingController::class, 'downloadReceiptPdf'])
            ->name('bookings.receipt.download');

        // Cart (public controller) — SOLO autenticados
        Route::get('/my-cart', [PublicCartController::class, 'index'])->name('public.carts.index');
        Route::post('/carts/add', [PublicCartController::class, 'store'])->name('public.carts.add');
        Route::delete('/carts/{item}', [PublicCartController::class, 'destroy'])->name('public.carts.destroy');
        Route::put('/carts/{item}', [PublicCartController::class, 'update'])->name('public.carts.update');

        // Timer / expiry (no duplicar en /public/*)
        Route::post('/carts/expire', [PublicCartController::class, 'expire'])->name('public.carts.expire');
        Route::post('/carts/refresh-expiry', [PublicCartController::class, 'refreshExpiry'])->name('public.carts.refreshExpiry');

        // Promo público (en sesión)
        Route::post('/apply-promo', [PublicCartController::class, 'applyPromo'])->name('public.carts.applyPromo');
        Route::delete('/remove-promo', [PublicCartController::class, 'removePromo'])->name('public.carts.removePromo');

        // Checkout desde carrito (público)
        Route::post('/bookings/from-cart', [PublicBookingController::class, 'storeFromCart'])->name('public.bookings.storeFromCart');

        // Checkout: vista de términos + proceso de aceptación (desplazar/leer + checkbox)
        Route::get('/checkout', [PublicCheckoutController::class, 'show'])->name('public.checkout.show');
        Route::post('/checkout/process', [PublicCheckoutController::class, 'process'])->name('public.checkout.process');
    });

    // ------------------------------
    // Admin
    // ------------------------------
    Route::middleware(['auth', 'verified', 'can:access-admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // ============================
            // ADMIN PROFILE
            // ============================
            Route::get('/profile', [ProfileController::class, 'adminShow'])->name('profile.show');
            Route::get('/profile/edit', [ProfileController::class, 'adminEdit'])->name('profile.edit');
            Route::post('/profile/edit', [ProfileController::class, 'adminUpdate'])->name('profile.update');

            // ============================
            // 2FA PROTECTED ROUTES
            // ============================
            Route::middleware('2fa.admin')->group(function () {

                // Dashboard
                Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

                // ============================
                // USERS & ROLES
                // ============================
                Route::resource('users', UserRegisterController::class)->except(['show']);
                Route::patch('users/{user}/lock', [UserRegisterController::class, 'lock'])->name('users.lock');
                Route::patch('users/{user}/unlock', [UserRegisterController::class, 'unlock'])->name('users.unlock');
                Route::patch('users/{user}/mark-verified', [UserRegisterController::class, 'markVerified'])->name('users.markVerified');

                Route::resource('roles', RoleController::class)->except(['show', 'create']);
                Route::patch('roles/{role}/toggle', [RoleController::class, 'toggle'])->name('roles.toggle');

                // ============================
                // CUSTOMER CATEGORIES
                // ============================
                Route::resource('customer_categories', CustomerCategoryController::class)
                    ->parameters(['customer_categories' => 'category']);
                Route::post('customer_categories/{category}/toggle', [CustomerCategoryController::class, 'toggle'])
                    ->name('customer_categories.toggle');

                // ============================
                // API AJAX (tours data) — UNIFICADO
                // ============================
                Route::prefix('api')->name('api.')->group(function () {
                    Route::get('tours/{tour}/schedules', [TourDataController::class, 'schedules'])->name('tours.schedules');
                    Route::get('tours/{tour}/languages', [TourDataController::class, 'languages'])->name('tours.languages');
                    Route::get('tours/{tour}/categories', [TourDataController::class, 'categories'])->name('tours.categories');
                });

                // ============================
                // TOURS
                // ============================
                Route::prefix('tours')->name('tours.')->group(function () {

                    // -------------------- TOUR MAIN CRUD --------------------
                    Route::get('/', [TourController::class, 'index'])->name('index');
                    Route::get('/create', [TourController::class, 'create'])->name('create');
                    Route::post('/', [TourController::class, 'store'])->name('store');
                    Route::get('/{tour}/edit', [TourController::class, 'edit'])->name('edit');
                    Route::put('/{tour}', [TourController::class, 'update'])->name('update');
                    Route::patch('/{tour}/toggle', [TourController::class, 'toggle'])->name('toggle');
                    Route::delete('/{tour}', [TourController::class, 'destroy'])->name('destroy');
                    Route::post('/{tour}/restore', [TourController::class, 'restore'])->name('restore');
                    Route::delete('/{tour}/purge', [TourController::class, 'purge'])->name('purge');

                    // 🆕 Rutas AJAX (nuevas)
                    Route::prefix('ajax')->name('ajax.')->group(function () {
                        Route::get('/validate-slug', [TourAjaxController::class, 'validateSlug'])->name('validate-slug');
                        Route::post('/create-category', [TourAjaxController::class, 'createCategory'])->name('create-category');
                        Route::post('/create-language', [TourAjaxController::class, 'createLanguage'])->name('create-language');
                        Route::post('/create-amenity', [TourAjaxController::class, 'createAmenity'])->name('create-amenity');
                        Route::post('/create-schedule', [TourAjaxController::class, 'createSchedule'])->name('create-schedule');
                        Route::post('/create-itinerary', [TourAjaxController::class, 'createItinerary'])->name('create-itinerary');
                        Route::post('/preview-translations', [TourAjaxController::class, 'previewTranslations'])->name('preview-translations');
                        Route::get('/load-itinerary/{itinerary}', [TourAjaxController::class, 'loadItinerary'])->name('load-itinerary');
                    });

                    // -------------------- TOUR ORDER --------------------
                    Route::get('/order', [TourOrderController::class, 'index'])->name('order.index');
                    Route::post('/order/{tourType}/save', [TourOrderController::class, 'save'])->name('order.save');

                    // -------------------- PRICES (por categoría) --------------------
                    Route::prefix('{tour}/prices')->name('prices.')->group(function () {
                        Route::get('/', [TourPriceController::class, 'index'])->name('index');
                        Route::post('/', [TourPriceController::class, 'store'])->name('store');
                        Route::post('/bulk-update', [TourPriceController::class, 'bulkUpdate'])->name('bulk-update');
                        Route::put('/{price}', [TourPriceController::class, 'update'])->name('update');
                        Route::post('/{price}/toggle', [TourPriceController::class, 'toggle'])->name('toggle');
                        Route::delete('/{price}', [TourPriceController::class, 'destroy'])->name('destroy');
                    });

                    // -------------------- IMAGES --------------------
                    Route::get('/images', [TourImageController::class, 'pick'])->name('images.pick');
                    Route::prefix('{tour}/images')->name('images.')->group(function () {
                        Route::get('/', [TourImageController::class, 'index'])->name('index');
                        Route::post('/', [TourImageController::class, 'store'])->name('store');
                        Route::delete('/', [TourImageController::class, 'bulkDestroy'])->name('bulk-destroy');
                        Route::delete('/all', [TourImageController::class, 'destroyAll'])->name('destroyAll');
                        Route::patch('/{image}', [TourImageController::class, 'update'])->name('update');
                        Route::delete('/{image}', [TourImageController::class, 'destroy'])->name('destroy');
                        Route::post('/{image}/cover', [TourImageController::class, 'setCover'])->name('cover');
                        Route::post('/reorder', [TourImageController::class, 'reorder'])->name('reorder');
                    });

                    // -------------------- SCHEDULES (Horarios) --------------------
                    Route::prefix('schedule')->name('schedule.')->group(function () {
                        Route::get('/', [TourScheduleController::class, 'index'])->name('index');
                        Route::post('/', [TourScheduleController::class, 'store'])->name('store');
                        Route::put('/{schedule}', [TourScheduleController::class, 'update'])->name('update');
                        Route::delete('/{schedule}', [TourScheduleController::class, 'destroy'])->name('destroy');
                        Route::put('/{schedule}/toggle', [TourScheduleController::class, 'toggle'])->name('toggle');

                        // Asignación a tours
                        Route::post('/{tour}/attach', [TourScheduleController::class, 'attach'])->name('attach');
                        Route::delete('/{tour}/{schedule}/detach', [TourScheduleController::class, 'detach'])->name('detach');
                        Route::patch('/{tour}/{schedule}/assignment-toggle', [TourScheduleController::class, 'toggleAssignment'])->name('assignment.toggle');

                        // 🆕 ACTUALIZAR CAPACIDAD DEL PIVOTE (tour+schedule)
                        Route::patch('/{tour}/{schedule}/pivot', [TourScheduleController::class, 'updatePivotCapacity'])
                            ->name('update-pivot-capacity');
                    });

                    // -------------------- CAPACITY MANAGEMENT --------------------
                    Route::prefix('capacity')->name('capacity.')->group(function () {
                        // Vista principal con tabs
                        Route::get('/', [TourAvailabilityController::class, 'index'])->name('index');

                        // Capacidad GLOBAL del tour
                        Route::patch('/tour/{tour}', [TourAvailabilityController::class, 'updateTourCapacity'])->name('update-tour');

                        // Capacidad BASE por horario (pivot)
                        Route::patch('/tour/{tour}/schedule/base-capacity', [TourAvailabilityController::class, 'updateScheduleBaseCapacity'])->name('update-schedule-base');

                        // Override puntual por día+horario
                        Route::post('/tour/{tour}/overrides/day-schedule', [TourAvailabilityController::class, 'upsertDayScheduleOverride'])->name('override-day-schedule');

                        // Bloqueo puntual por día+horario
                        Route::post('/tour/{tour}/overrides/day-schedule/toggle-block', [TourAvailabilityController::class, 'toggleBlockDaySchedule'])->name('toggle-block-day-schedule');

                        // ===== WIDGET DE ALERTAS (CapacityController) =====
                        Route::prefix('schedules/{schedule}')->group(function () {
                            Route::patch('/increase', [CapacityController::class, 'increase'])->name('increase');
                            Route::get('/details', [CapacityController::class, 'show'])->name('details');
                            Route::patch('/block', [CapacityController::class, 'block'])->name('block');
                        });

                        // Legacy CRUD directo
                        Route::post('/', [TourAvailabilityController::class, 'store'])->name('store');
                        Route::patch('/{availability}', [TourAvailabilityController::class, 'update'])->name('update');
                        Route::delete('/{availability}', [TourAvailabilityController::class, 'destroy'])->name('destroy');
                    });

                    // -------------------- EXCLUDED DATES --------------------
                    Route::prefix('excluded_dates')->name('excluded_dates.')->group(function () {
                        Route::get('/', [TourExcludedDateController::class, 'index'])->name('index');
                        Route::get('/blocked', [TourExcludedDateController::class, 'blocked'])->name('blocked');
                        Route::post('/', [TourExcludedDateController::class, 'store'])->name('store');
                        Route::put('/{excludedDate}', [TourExcludedDateController::class, 'update'])->name('update');
                        Route::delete('/{excludedDate}', [TourExcludedDateController::class, 'destroy'])->name('destroy');
                        Route::post('/toggle', [TourExcludedDateController::class, 'toggle'])->name('toggle');
                        Route::post('/bulk-toggle', [TourExcludedDateController::class, 'bulkToggle'])->name('bulkToggle');
                        Route::post('/block-all', [TourExcludedDateController::class, 'blockAll'])->name('blockAll');
                        Route::post('/store-multiple', [TourExcludedDateController::class, 'storeMultiple'])->name('storeMultiple');
                        Route::delete('/all', [TourExcludedDateController::class, 'destroyAll'])->name('destroyAll');
                        Route::post('/destroy-selected', [TourExcludedDateController::class, 'destroySelected'])->name('destroySelected');
                    });

                    // -------------------- CUTOFF --------------------
                    Route::prefix('cutoff')->name('cutoff.')->group(function () {
                        Route::get('/', [CutOffController::class, 'edit'])->name('edit');
                        Route::put('/', [CutOffController::class, 'update'])->name('update');
                        Route::put('/tour', [CutOffController::class, 'updateTourOverrides'])->name('tour.update');
                        Route::put('/schedule', [CutOffController::class, 'updateScheduleOverrides'])->name('schedule.update');
                    });

                    // -------------------- ITINERARY --------------------
                    Route::resource('itinerary', ItineraryController::class)->except(['show']);
                    Route::patch('itineraries/{itinerary}/toggle', [ItineraryController::class, 'toggle'])->name('itinerary.toggle');
                    Route::post('itinerary/{itinerary}/assign-items', [ItineraryController::class, 'assignItems'])->name('itinerary.assignItems');

                    // -------------------- ITINERARY ITEMS --------------------
                    Route::resource('itinerary_items', ItineraryItemController::class)->except(['show', 'create', 'edit']);
                    Route::patch('itinerary_items/{itinerary_item}/toggle', [ItineraryItemController::class, 'toggle'])->name('itinerary_items.toggle');

                    // -------------------- AMENITIES --------------------
                    Route::resource('amenities', AmenityController::class)->except(['show']);
                    Route::patch('amenities/{amenity}/toggle', [AmenityController::class, 'toggle'])->name('amenities.toggle');
                });

                // ============================
                // TOUR TYPES
                // ============================
                Route::resource('tourtypes', TourTypeController::class, ['parameters' => ['tourtypes' => 'tourType']])->except(['show']);
                Route::put('tourtypes/{tourType}/toggle', [TourTypeController::class, 'toggle'])->name('tourtypes.toggle');

                // Tour Type Images
                Route::prefix('types')->name('types.')->group(function () {
                    Route::get('images', [TourTypeCoverPickerController::class, 'pick'])->name('images.pick');
                    Route::get('images/{tourType}/edit', [TourTypeCoverPickerController::class, 'edit'])->name('images.edit');
                    Route::put('images/{tourType}', [TourTypeCoverPickerController::class, 'updateCover'])->name('images.update');
                });

                // ============================
                // LANGUAGES
                // ============================
                Route::resource('languages', TourLanguageController::class, ['parameters' => ['languages' => 'language']])->except(['show']);
                Route::patch('languages/{language}/toggle', [TourLanguageController::class, 'toggle'])->name('languages.toggle');

                // ============================
                // HOTELS
                // ============================
                Route::resource('hotels', HotelListController::class)->except(['show', 'create', 'edit']);
                Route::post('hotels/sort', [HotelListController::class, 'sort'])->name('hotels.sort');
                Route::patch('hotels/{hotel}/toggle', [HotelListController::class, 'toggle'])->name('hotels.toggle');

                // ============================
                // MEETING POINTS
                // ============================
                Route::resource('meetingpoints', MeetingPointSimpleController::class)->except(['show', 'create', 'edit']);
                Route::patch('meetingpoints/{meetingpoint}/toggle', [MeetingPointSimpleController::class, 'toggle'])->name('meetingpoints.toggle');

                // ============================
                // BOOKINGS (ADMIN)
                // ============================
                Route::prefix('bookings')
                    ->name('bookings.')
                    ->controller(AdminBookingController::class)
                    ->group(function () {
                        // Export
                        Route::get('export/excel', 'exportExcel')->name('export.excel');
                        Route::get('export/pdf', 'exportPdf')->name('export.pdf');

                        // Calendar
                        Route::get('reserved', 'reservedSeats')->name('reserved');
                        Route::get('calendar-data', 'calendarData')->name('calendarData');
                        Route::get('calendar', 'calendar')->name('calendar');

                        // API promo verification
                        Route::get('verify-promo-code', 'verifyPromoCode')->name('verifyPromoCode');

                        // CRUD principal
                        Route::get('', 'index')->name('index');
                        Route::get('create', 'create')->name('create');
                        Route::post('', 'store')->name('store');
                        Route::post('from-cart', 'storeFromCart')->name('storeFromCart');
                        Route::get('{booking}/edit', 'edit')->name('edit');
                        Route::match(['put', 'patch'], '{booking}', 'update')->name('update');
                        Route::delete('{booking}', 'destroy')->name('destroy');

                        // Estado y recibo
                        Route::patch('{booking}/status', 'updateStatus')->name('update-status');
                        Route::get('{booking}/receipt', 'generateReceipt')->name('receipt');
                    });

                // ============================
                // CART (ADMIN)
                // ============================
                Route::prefix('carts')->name('carts.')->group(function () {
                    Route::get('/', [AdminCartController::class, 'index'])->name('index');
                    Route::post('/', [AdminCartController::class, 'store'])->name('store');
                    Route::patch('/{item}', [AdminCartController::class, 'update'])->name('update');
                    Route::delete('/item/{item}', [AdminCartController::class, 'destroy'])->name('item.destroy');

                    Route::get('/all', [AdminCartController::class, 'allCarts'])->name('all');
                    Route::delete('/{cart}', [AdminCartController::class, 'destroyCart'])->name('destroy');
                    Route::patch('/{cart}/toggle', [AdminCartController::class, 'toggleActive'])->name('toggle');

                    Route::post('/apply-promo', [AdminCartController::class, 'applyPromoAdmin'])->name('applyPromo');
                    Route::delete('/remove-promo', [AdminCartController::class, 'removePromoAdmin'])->name('removePromo');
                });

                // ============================
                // PROMO CODES
                // ============================
                Route::prefix('promo-codes')->name('promoCodes.')->group(function () {
                    Route::get('/', [PromoCodeController::class, 'index'])->name('index');
                    Route::post('/', [PromoCodeController::class, 'store'])->name('store');
                    Route::delete('/{promo}', [PromoCodeController::class, 'destroy'])->name('destroy');
                    Route::patch('/{promo}/operation', [PromoCodeController::class, 'updateOperation'])->name('updateOperation');
                });

                // ============================
                // REVIEWS MANAGEMENT
                // ============================
                Route::middleware(['can:manage-reviews'])->group(function () {
                    // Providers
                    Route::resource('review-providers', ReviewProviderController::class)
                        ->except(['show'])
                        ->parameters(['review-providers' => 'provider'])
                        ->names('review-providers');
                    Route::post('review-providers/{provider}/toggle', [ReviewProviderController::class, 'toggle'])->name('review-providers.toggle');
                    Route::post('review-providers/{provider}/test', [ReviewProviderController::class, 'test'])->name('review-providers.test');
                    Route::post('review-providers/{provider}/cache/flush', [ReviewProviderController::class, 'flushCache'])->name('review-providers.flush');

                    // Reviews
                    Route::prefix('reviews')->name('reviews.')->group(function () {
                        Route::get('/', [ReviewAdminController::class, 'index'])->name('index');
                        Route::get('/create', [ReviewAdminController::class, 'create'])->name('create');
                        Route::post('/', [ReviewAdminController::class, 'store'])->name('store');
                        Route::get('/{review}/edit', [ReviewAdminController::class, 'edit'])->name('edit');
                        Route::put('/{review}', [ReviewAdminController::class, 'update'])->name('update');
                        Route::delete('/{review}', [ReviewAdminController::class, 'destroy'])->name('destroy');

                        Route::post('/{review}/publish', [ReviewAdminController::class, 'publish'])->name('publish');
                        Route::post('/{review}/hide', [ReviewAdminController::class, 'hide'])->name('hide');
                        Route::post('/{review}/flag', [ReviewAdminController::class, 'flag'])->name('flag');
                        Route::post('/bulk', [ReviewAdminController::class, 'bulk'])->name('bulk');

                        // Replies & Threads
                        Route::get('/{review}/replies/create', [ReviewReplyController::class, 'create'])->name('replies.create');
                        Route::post('/{review}/replies', [ReviewReplyController::class, 'store'])->name('replies.store');
                        Route::delete('/{review}/replies/{reply}', [ReviewReplyController::class, 'destroy'])->name('replies.destroy');
                        Route::post('/{review}/replies/{reply}/toggle', [ReviewReplyController::class, 'toggle'])->name('replies.toggle');
                        Route::get('/{review}/thread', [ReviewReplyController::class, 'thread'])->name('replies.thread');
                    });

                    // Review Requests
                    Route::prefix('review-requests')->name('review-requests.')->group(function () {
                        Route::get('/', [ReviewRequestAdminController::class, 'index'])->name('index');
                        Route::post('/{booking}/send', [ReviewRequestAdminController::class, 'send'])->name('send');
                        Route::post('/{rr}/resend', [ReviewRequestAdminController::class, 'resend'])->name('resend');
                        Route::delete('/{rr}', [ReviewRequestAdminController::class, 'destroy'])->name('destroy');
                    });
                });

                // ============================
                // FAQs
                // ============================
                Route::resource('faqs', AdminFaqController::class)->except(['show']);
                Route::patch('faqs/{faq}/toggle-status', [AdminFaqController::class, 'toggleStatus'])->name('faqs.toggleStatus');

                // ============================
                // POLICIES
                // ============================
                Route::prefix('policies')->name('policies.')->group(function () {
                    Route::get('/', [PolicyController::class, 'index'])->name('index');
                    Route::post('/', [PolicyController::class, 'store'])->name('store');
                    Route::put('/{policy:policy_id}', [PolicyController::class, 'update'])->name('update');
                    Route::post('/{policy:policy_id}/toggle', [PolicyController::class, 'toggle'])->name('toggle');
                    Route::delete('/{policy:policy_id}', [PolicyController::class, 'destroy'])->name('destroy');

                    // Policy Sections
                    Route::get('/{policy:policy_id}/sections', [PolicySectionController::class, 'index'])->name('sections.index');
                    Route::post('/{policy:policy_id}/sections', [PolicySectionController::class, 'store'])->name('sections.store');
                    Route::put('/{policy:policy_id}/sections/{section}', [PolicySectionController::class, 'update'])->name('sections.update');
                    Route::post('/{policy:policy_id}/sections/{section}/toggle', [PolicySectionController::class, 'toggle'])->name('sections.toggle');
                    Route::delete('/{policy:policy_id}/sections/{section}', [PolicySectionController::class, 'destroy'])->name('sections.destroy');
                });

                // ============================
                // TRANSLATIONS
                // ============================
                Route::prefix('translations')->name('translations.')->group(function () {
                    Route::get('/', [TranslationController::class, 'index'])->name('index');
                    Route::get('/{type}/choose-locale', [TranslationController::class, 'chooseLocale'])->name('choose-locale');
                    Route::get('/{type}/select', [TranslationController::class, 'select'])->name('select');
                    Route::get('/{type}/{id}/edit', [TranslationController::class, 'edit'])->name('edit');
                    Route::post('/{type}/{id}/update', [TranslationController::class, 'update'])->name('update');
                    Route::post('/change-editing-locale', [TranslationController::class, 'changeEditingLocale'])->name('change-editing-locale');
                });

                // ============================
                // REPORTS
                // ============================
                Route::prefix('reports')->name('reports.')->group(function () {
                    Route::get('/', [ReportsController::class, 'index'])->name('index');
                    Route::get('/chart/monthly-sales', [ReportsController::class, 'chartMonthlySales'])->name('chart.monthly');
                    Route::get('/chart/by-language', [ReportsController::class, 'chartByLanguage'])->name('chart.language');
                });
            });
        });
});

// ============================
// COOKIES (consent) - Outside admin middleware
// ============================
Route::post('/cookies/accept', [CookieConsentController::class, 'accept'])
    ->name('cookies.accept')
    ->middleware('throttle:10,1');

Route::post('/cookies/reject', [CookieConsentController::class, 'reject'])
    ->name('cookies.reject')
    ->middleware('throttle:10,1');
