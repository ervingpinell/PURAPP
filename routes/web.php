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
use App\Http\Controllers\Admin\AuditController;


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
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\EmailChangeController;
use App\Http\Controllers\Admin\Tours\TourWizardController;
use App\Http\Controllers\Admin\TaxController;


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
    function localizedRoutes(\Closure $callback)
    {
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
| Shortcuts sin locale -> con locale
|--------------------------------------------------------------------------
*/

// /tours -> /{locale}/tours
Route::get('/tours', function () {
    $locale = session('locale', config('routes.default_locale', 'es'));
    return redirect("/{$locale}/tours");
})->name('tours.shortcut');

// /faq -> /{locale}/faq
Route::get('/faq', function () {
    $locale = session('locale', config('routes.default_locale', 'es'));
    return redirect("/{$locale}/faq");
})->name('faq.shortcut');

// /faqs -> /{locale}/faq (alias)
Route::get('/faqs', function () {
    $locale = session('locale', config('routes.default_locale', 'es'));
    return redirect("/{$locale}/faq");
})->name('faqs.shortcut');

// /reviews -> /{locale}/reviews
Route::get('/reviews', function () {
    $locale = session('locale', config('routes.default_locale', 'es'));
    return redirect("/{$locale}/reviews");
})->name('reviews.shortcut');

// /home -> /{locale}/home
Route::get('/home', function () {
    $locale = session('locale', config('routes.default_locale', 'es'));
    return redirect("/{$locale}");
})->name('home.shortcut');

// /contact -> /{locale}/contact
Route::get('/contact', function () {
    $locale = session('locale', config('routes.default_locale', 'es'));
    return redirect("/{$locale}/contact");
})->name('contact.shortcut');

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
| Payment Webhooks (no auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('webhooks/payment')->name('webhooks.payment.')->group(function () {
    Route::post('/stripe', [\App\Http\Controllers\Webhooks\PaymentWebhookController::class, 'stripe'])->name('stripe');
    Route::post('/tilopay', [\App\Http\Controllers\Webhooks\PaymentWebhookController::class, 'tilopay'])->name('tilopay');
    Route::post('/banco-nacional', [\App\Http\Controllers\Webhooks\PaymentWebhookController::class, 'bancoNacional'])->name('banco_nacional');
    Route::post('/bac', [\App\Http\Controllers\Webhooks\PaymentWebhookController::class, 'bac'])->name('bac');
    Route::post('/bcr', [\App\Http\Controllers\Webhooks\PaymentWebhookController::class, 'bcr'])->name('bcr');
});

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

        Route::get('/tours', action: [HomeController::class, 'allTours'])->name('tours.index');
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
    Route::get('/auth/throttled', fn() => response()->view('errors.429'))->name('auth.throttled');

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

        // Checkout
        Route::get('/checkout', [PublicCheckoutController::class, 'show'])->name('public.checkout.show');
        Route::post('/checkout/process', [PublicCheckoutController::class, 'process'])
            ->name('public.checkout.process');
        Route::post('/checkout/accept-terms', [PublicCheckoutController::class, 'acceptTerms'])->name('public.checkout.accept-terms');

        // Payment
        Route::get('/payment', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/initiate', [\App\Http\Controllers\PaymentController::class, 'initiate'])
            ->middleware('throttle:10,1')
            ->name('payment.initiate');
        Route::get('/payment/confirm', [\App\Http\Controllers\PaymentController::class, 'confirm'])->name('payment.confirm');
        Route::get('/payment/return', [\App\Http\Controllers\PaymentController::class, 'confirm'])->name('payment.return'); // PayPal return URL
        Route::get('/payment/cancel', [\App\Http\Controllers\PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::get('/payment/{payment}/status', [\App\Http\Controllers\PaymentController::class, 'status'])->name('payment.status');
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
                // TAXES
                // ============================
                Route::resource('taxes', TaxController::class);
                Route::post('taxes/{tax}/toggle', [TaxController::class, 'toggle'])->name('taxes.toggle');

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

                    // -------------------- CUTOFF --------------------
                    // Moved here to avoid collision with /{tour} wildcard
                    Route::prefix('cutoff')->name('cutoff.')->group(function () {
                        Route::get('/', [CutOffController::class, 'edit'])->name('edit');
                        Route::match(['put', 'post'], '/', [CutOffController::class, 'update'])->name('update');
                        Route::match(['put', 'post'], '/tour', [CutOffController::class, 'updateTourOverrides'])->name('tour.update');
                        Route::match(['put', 'post'], '/schedule', [CutOffController::class, 'updateScheduleOverrides'])->name('schedule.update');
                    });

                    // -------------------- TOUR MAIN CRUD --------------------
                    Route::get('/', [TourController::class, 'index'])->name('index');
                    Route::get('/create', [TourWizardController::class, 'create'])->name('create');
                    Route::post('/', [TourController::class, 'store'])->name('store');
                    Route::get('/{tour}/edit', [TourWizardController::class, 'edit'])->name('edit');
                    Route::put('/{tour}', [TourController::class, 'update'])->name('update');
                    Route::patch('/{tour}/toggle', [TourController::class, 'toggle'])->name('toggle');
                    Route::delete('/{tour}', [TourController::class, 'destroy'])->name('destroy');
                    Route::post('/{tour}/restore', [TourController::class, 'restore'])->name('restore');
                    Route::delete('/{tour}/purge', [TourController::class, 'purge'])->name('purge');

                    /**
                     * ============================================================
                     * TOUR WIZARD - Creación paso a paso con gestión de drafts
                     * ============================================================
                     */
                    Route::prefix('wizard')->name('wizard.')->group(function () {
                        // 🔄 CAMBIO: 'tours.wizard.' → 'wizard.'

                        // Paso inicial - Detecta drafts existentes
                        Route::get('/create', [TourWizardController::class, 'create'])->name('create');


                        // 🆕 Gestión de drafts
                        Route::get('/continue/{tour}', [TourWizardController::class, 'continueDraft'])
                            ->name('continue');
                        Route::delete('/delete-draft/{tour}', [TourWizardController::class, 'deleteDraft'])
                            ->name('delete-draft');
                        Route::delete('/delete-all-drafts', [TourWizardController::class, 'deleteAllDrafts'])
                            ->name('delete-all-drafts');


                        // Paso 1: Detalles básicos
                        Route::post('/store-details', [TourWizardController::class, 'storeDetails'])
                            ->middleware('throttle:10,1')
                            ->name('store.details');

                        Route::post('/{tour}/update-details', [TourWizardController::class, 'updateDetails'])
                            ->name('update.details');

                        // Navegación entre pasos
                        Route::get('/{tour}/step/{step}', [TourWizardController::class, 'showStep'])
                            ->name('step');

                        // Paso 2: Itinerario
                        Route::post('/{tour}/store-itinerary', [TourWizardController::class, 'storeItinerary'])
                            ->name('store.itinerary');

                        // Paso 3: Horarios
                        Route::post('/{tour}/store-schedules', [TourWizardController::class, 'storeSchedules'])
                            ->name('store.schedules');
                        Route::post('/{tour}/quick-schedule', [TourWizardController::class, 'quickStoreSchedule'])
                            ->name('quick.schedule');

                        // Paso 4: Amenidades
                        Route::post('/{tour}/store-amenities', [TourWizardController::class, 'storeAmenities'])
                            ->name('store.amenities');
                        Route::post('/quick-amenity', [TourWizardController::class, 'quickStoreAmenity'])
                            ->name('quick.amenity');

                        // Paso 5: Precios
                        Route::post('/{tour}/store-prices', [TourWizardController::class, 'storePrices'])
                            ->name('store.prices');
                        Route::post('/quick-category', [TourWizardController::class, 'quickStoreCategory'])
                            ->name('quick.category');

                        // Paso 6: Publicar
                        Route::post('/{tour}/publish', [TourWizardController::class, 'publish'])
                            ->name('publish');

                        // Quick creates (AJAX)
                        Route::post('/quick-tour-type', [TourWizardController::class, 'quickStoreTourType'])
                            ->name('quick.tour-type');
                        Route::post('/quick-language', [TourWizardController::class, 'quickStoreLanguage'])
                            ->name('quick.language');
                        Route::post('/quick-itinerary-item', [TourWizardController::class, 'quickCreateItineraryItem'])
                            ->name('quick.itinerary-item');
                    });

                    /**
                     * ============================================================
                     * 🆕 AUDITORÍA
                     * ============================================================
                     */
                    Route::prefix('audit')->name('audit.')->middleware('can:view-audit')->group(function () {
                        // 🔄 CAMBIO: Quitamos el prefijo 'tours.' duplicado

                        // Dashboard principal de auditoría
                        Route::get('/dashboard', [AuditController::class, 'dashboard'])
                            ->name('dashboard');

                        // Listado de logs con filtros
                        Route::get('/', [AuditController::class, 'index'])
                            ->name('index');

                        // Ver detalles de un log específico
                        Route::get('/log/{log}', [AuditController::class, 'show'])
                            ->name('show');

                        // Historial completo de un tour
                        Route::get('/tour/{tour}/history', [AuditController::class, 'tourHistory'])
                            ->name('tour-history');

                        // Actividad de un usuario
                        Route::get('/user/{user}/activity', [AuditController::class, 'userActivity'])
                            ->name('user-activity');

                        // Exportar logs
                        Route::get('/export', [AuditController::class, 'export'])
                            ->name('export');

                        // Limpiar logs antiguos (solo administradores)
                        Route::delete('/purge', [AuditController::class, 'purge'])
                            ->middleware('can:purge-audit')
                            ->name('purge');
                    });

                    /**
                     * ============================================================
                     * 🆕 ESTADÍSTICAS Y REPORTES
                     * ============================================================
                     */
                    Route::prefix('stats')->name('stats.')->group(function () {
                        // 🔄 CAMBIO: 'tours/stats' → 'stats' (ya estamos en el grupo 'tours')

                        Route::get('/drafts', [TourController::class, 'draftsStats'])
                            ->name('drafts');
                        Route::get('/users', [TourController::class, 'usersStats'])
                            ->name('users');
                        Route::get('/activity', [TourController::class, 'activityStats'])
                            ->name('activity');
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
                        Route::post('/update-taxes', [TourPriceController::class, 'updateTaxes'])->name('update-taxes');
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



                    // -------------------- ITINERARY --------------------
                    Route::resource('itinerary', ItineraryController::class)->except(['show']);
                    Route::patch('itineraries/{itinerary}/toggle', [ItineraryController::class, 'toggle'])->name('itinerary.toggle');
                    Route::post('itinerary/{itinerary}/assign-items', [ItineraryController::class, 'assignItems'])->name('itinerary.assignItems');
                    Route::put('itinerary/{itinerary}/translations', [ItineraryController::class, 'updateTranslations'])->name('itinerary.updateTranslations');

                    // -------------------- ITINERARY ITEMS --------------------
                    Route::resource('itinerary_items', ItineraryItemController::class)->except(['show', 'create', 'edit']);
                    Route::patch('itinerary_items/{itinerary_item}/toggle', [ItineraryItemController::class, 'toggle'])->name('itinerary_items.toggle');
                    Route::put('itinerary_items/{itinerary_item}/translations', [ItineraryItemController::class, 'updateTranslations'])->name('itinerary_items.updateTranslations');

                    // -------------------- PRICES --------------------
                    Route::post('prices/check-overlap', [TourPriceController::class, 'checkOverlap'])
                        ->name('prices.check-overlap');
                    Route::resource('prices', TourPriceController::class)->except(['show', 'edit', 'create']);
                    Route::put('prices/{price}/toggle', [TourPriceController::class, 'toggle'])->name('prices.toggle');

                    // -------------------- AMENITIES --------------------
                    Route::resource('amenities', AmenityController::class)->except(['show']);
                    Route::patch('amenities/{amenity}/toggle', [AmenityController::class, 'toggle'])->name('amenities.toggle');
                });


                // ============================
                // TOUR TYPES
                // ============================
                // Rutas de gestión de traducciones (antes del resource)
                Route::get('tourtypes/{tourType}/translations', [TourTypeController::class, 'editTranslations'])
                    ->name('tourtypes.translations.edit');
                Route::put('tourtypes/{tourType}/translations/{locale}', [TourTypeController::class, 'updateTranslation'])
                    ->name('tourtypes.translations.update');

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

                        // NUEVO: detalle (show)
                        Route::get('{booking}', 'show')->name('show');

                        // SoftDelete actions
                        Route::post('{id}/restore', 'restore')->name('restore');
                        Route::delete('{id}/force', 'forceDelete')->name('forceDelete');

                        // Estado y recibo
                        Route::patch('{booking}/status', 'updateStatus')->name('update-status');
                        Route::get('{booking}/receipt', 'generateReceipt')->name('receipt');
                    });

                // ============================
                // PAYMENTS (ADMIN)
                // ============================
                Route::prefix('payments')
                    ->name('payments.')
                    ->controller(\App\Http\Controllers\Admin\PaymentController::class)
                    ->group(function () {
                        // Export
                        Route::get('export', 'export')->name('export');

                        // List and details
                        Route::get('', 'index')->name('index');
                        Route::get('{payment}', 'show')->name('show');

                        // Refund
                        Route::post('{payment}/refund', 'refund')->name('refund');
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
                // SETTINGS
                // ============================
                Route::prefix('settings')->name('settings.')->group(function () {
                    Route::get('/', [SettingsController::class, 'index'])->name('index');
                    Route::post('/', [SettingsController::class, 'update'])->name('update');
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
                    // Restaurar desde papelera
                    Route::post('/{policy:policy_id}/restore', [PolicyController::class, 'restore'])->name('restore');

                    // Borrado definitivo SOLO para admins (can:policies.forceDelete)
                    Route::delete('/{policy:policy_id}/force', [PolicyController::class, 'forceDestroy'])
                        ->middleware('can:policies.forceDelete')
                        ->name('forceDestroy');

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

                    // 🆕 Category Reports
                    Route::get('/by-category', [ReportsController::class, 'byCategory'])->name('by-category');
                    Route::get('/chart/category-trends', [ReportsController::class, 'chartCategoryTrends'])->name('chart.category-trends');
                    Route::get('/chart/category-breakdown', [ReportsController::class, 'chartCategoryBreakdown'])->name('chart.category-breakdown');

                    // 🆕 Exports
                    Route::get('/export/categories-excel', [ReportsController::class, 'exportCategoriesExcel'])->name('export.categories.excel');
                    Route::get('/export/categories-csv', [ReportsController::class, 'exportCategoriesCsv'])->name('export.categories.csv');
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

// Temporary Test Route for PayPal
Route::get('/test-paypal', function () {
    try {
        $gateway = app(\App\Services\PaymentGateway\PaymentGatewayManager::class)->driver('paypal');
        return 'PayPal Gateway Initialized Successfully. Check logs for details.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage() . '<br>' . $e->getTraceAsString();
    }
});
