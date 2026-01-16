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

// Preview routes moved to Admin Debug section (see below)

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
        '',
        'Sitemap: ' . url('sitemap.xml'),
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
    // Session is guaranteed to be set by SetLocale middleware
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
    ->middleware('throttle:public');

/*
|--------------------------------------------------------------------------
| Payment Webhooks (no auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('webhooks/payment')->name('webhooks.payment.')->group(function () {
    Route::post('/stripe', [\App\Http\Controllers\Webhooks\PaymentWebhookController::class, 'stripe'])->name('stripe');
    // Alignet webhook is defined below with CORS middleware
});

/*
|--------------------------------------------------------------------------
| Public Payment Token Route (no auth required)
|--------------------------------------------------------------------------
*/
Route::get('/pay/{token}', [\App\Http\Controllers\PaymentController::class, 'showByToken'])
    ->middleware([SetLocale::class])
    ->name('payment.show-by-token');

Route::post('/booking/{token}/complete-info', [\App\Http\Controllers\PaymentController::class, 'updateBookingUserInfo'])
    ->middleware([SetLocale::class])
    ->name('booking.complete-info');


// Terms & Conditions Route (Redirects to policies for now)
Route::get('/terms', function () {
    return redirect()->route('policies.index');
})->name('public.terms');

// Payment initiate - works for both authenticated and token-based payments
Route::post('/payment/record-terms', [\App\Http\Controllers\PaymentController::class, 'recordTerms'])
    ->middleware('throttle:payment')
    ->name('payment.record-terms');

Route::post('/payment/initiate', [\App\Http\Controllers\PaymentController::class, 'initiate'])
    ->middleware('throttle:payment-initiate')
    ->name('payment.initiate');

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
        // Main route: /{locale}  -> es.home, en.home, etc.
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // Optional alias /{locale}/home, WITHOUT duplicate name
        Route::get('/home', [HomeController::class, 'index']);

        Route::get('/tours', action: [HomeController::class, 'allTours'])->name('tours.index');
        Route::get('/tours/{tour:slug}', [HomeController::class, 'showTour'])->name('tours.show');

        // ============================
        // Contact
        // ============================
        Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

        // FAQ
        Route::view('/faq', 'public.faq')->name('faq');
        Route::post('/contact', [HomeController::class, 'sendContact'])
            ->middleware('throttle:sensitive')
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
    Route::post('/reviews', [ReviewsController::class, 'store'])
        ->middleware('throttle:sensitive')
        ->name('reviews.store');

    // Embed with NOINDEX
    Route::get('/reviews/embed/{provider}', function (Request $request, ReviewAggregator $agg, string $provider) {
        $response = app(ReviewsController::class)->embed($request, $agg, $provider);
        return $response->header('X-Robots-Tag', 'noindex, nofollow');
    })->where('provider', '[A-Za-z0-9_-]+')->name('reviews.embed');

    Route::get('/r/{token}', [PublicReviewController::class, 'show'])->name('reviews.request.show');
    Route::post('/r/{token}', [PublicReviewController::class, 'submit'])
        ->middleware('throttle:sensitive')
        ->name('reviews.request.submit');
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
        ->middleware('throttle:email')
        ->name('unlock.send');
    Route::get('/unlock-account/{user}/{hash}', [UnlockAccountController::class, 'process'])
        ->middleware('signed')
        ->name('unlock.process');

    // Email verification - Public verification URL (from email)
    Route::get('/email/verify/public/{id}/{hash}', PublicEmailVerificationController::class)
        ->middleware(['signed', 'throttle:auth'])
        ->name('verification.public');

    // 🆕 Email verification - Notice screen (after registration)
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('guest')->name('verification.notice');

    Route::get('/email/change/confirm/{user}/{token}', [EmailChangeController::class, 'confirm'])
        ->middleware('signed')
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
    })->middleware('throttle:email')->name('verification.public.resend');

    // ------------------------------
    // Promo codes (public - works for guests AND auth users)
    // ------------------------------
    Route::post('/apply-promo', [PublicCartController::class, 'applyPromo'])
        ->middleware('throttle:promo')
        ->name('public.carts.applyPromo');
    Route::delete('/remove-promo', [PublicCartController::class, 'removePromo'])
        ->middleware('throttle:promo')
        ->name('public.carts.removePromo');

    // ------------------------------
    // Profile & cart (private) — READONLY-BLOCKABLE
    // ------------------------------
    Route::middleware(['auth', 'verified'])->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit', [ProfileController::class, 'update'])
            ->middleware('throttle:sensitive')
            ->name('profile.update');

        // My bookings (public controller)
        Route::get('/my-bookings', [PublicBookingController::class, 'myBookings'])->name('my-bookings');
        Route::get('/my-bookings/{booking}/receipt', [PublicBookingController::class, 'downloadReceiptPdf'])
            ->name('bookings.receipt.download');

        // Cart (public controller) — SOLO autenticados (excepto index, add, y update)
        Route::delete('/carts/{item}', [PublicCartController::class, 'destroy'])
            ->middleware('throttle:cart')
            ->name('public.carts.destroy');

        // Timer / expiry (no duplicar en /public/*)
        Route::post('/carts/expire', [PublicCartController::class, 'expire'])
            ->middleware('throttle:cart')
            ->name('public.carts.expire');
        Route::post('/carts/refresh-expiry', [PublicCartController::class, 'refreshExpiry'])
            ->middleware('throttle:cart')
            ->name('public.carts.refreshExpiry');

        // Guest cart expiration
        Route::post('/guest-cart/expire', [PublicCartController::class, 'expireGuest'])
            ->middleware('throttle:cart')
            ->name('public.guest-carts.expire');

        // Checkout desde carrito (público)
        Route::post('/bookings/from-cart', [PublicBookingController::class, 'storeFromCart'])
            ->middleware('throttle:payment')
            ->name('public.bookings.storeFromCart');

        // Pay-Later routes removed from auth group to allow guest access

        // Payment Links (Pay-Later System)
        Route::get('/booking/payment/{bookingReference}', [PublicCheckoutController::class, 'showPayment'])
            ->name('booking.payment');
        Route::post('/booking/payment-success', [PublicCheckoutController::class, 'handlePaymentSuccess'])
            ->middleware('throttle:payment')
            ->name('booking.payment.success');
    });

    // ------------------------------
    // Cart ADD route - Available for GUESTS (outside auth)
    // ------------------------------
    Route::post('/carts/add', [PublicCartController::class, 'store'])
        ->middleware('throttle.cart')  // 🆕 Custom anti-bot middleware
        ->name('public.carts.add');

    // ------------------------------
    // Cart Remove Guest Item - Available for GUESTS (outside auth)
    // ------------------------------
    Route::post('/carts/remove-guest-item', [PublicCartController::class, 'removeGuestItem'])
        ->middleware('throttle:cart')
        ->name('public.carts.removeGuestItem');

    // ------------------------------
    // Cart Update Item - Available for GUESTS (outside auth)
    // ------------------------------
    Route::put('/carts/{item}', [PublicCartController::class, 'update'])
        ->middleware('throttle:cart')
        ->name('public.carts.update');

    // ------------------------------
    // Checkout Routes - Available for GUESTS (outside auth)
    // ------------------------------
    Route::get('/checkout', [PublicCheckoutController::class, 'show'])->name('public.checkout.show');
    Route::post('/checkout/process', [PublicCheckoutController::class, 'process'])
        ->middleware('throttle:guest-checkout')
        ->name('public.checkout.process');
    Route::post('/checkout/accept-terms', [PublicCheckoutController::class, 'acceptTerms'])
        ->middleware('throttle:payment')
        ->name('public.checkout.accept-terms');

    // Checkout por token (para reservas creadas por admin - NO requiere auth)
    Route::get('/checkout/{token}', [PublicCheckoutController::class, 'showByToken'])->name('public.checkout.token');

    // ------------------------------
    // Cart View - Available for GUESTS (outside auth)
    // ------------------------------
    Route::get('/my-cart', [PublicCartController::class, 'index'])->name('public.carts.index');

    // ------------------------------
    // Payment Routes - Available for GUESTS (outside auth)
    // ------------------------------
    Route::get('/payment', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payment.show');
    Route::get('/payment/confirm', [\App\Http\Controllers\PaymentController::class, 'confirm'])->name('payment.confirm');
    Route::match(['get', 'post'], '/payment/return', [\App\Http\Controllers\PaymentController::class, 'confirm'])->name('payment.return'); // PayPal/Alignet return URL
    Route::get('/payment/return/restore/{token}', [\App\Http\Controllers\PaymentController::class, 'restoreSession'])
        ->middleware('throttle:10,1') // Max 10 attempts per minute per IP
        ->name('payment.return.restore'); // Alignet session restoration
    Route::get('/payment/cancel', [\App\Http\Controllers\PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::get('/payment/error', [\App\Http\Controllers\PaymentController::class, 'error'])->name('payment.error');
    Route::get('/payment/{payment}/status', [\App\Http\Controllers\PaymentController::class, 'status'])->name('payment.status');

    // Alignet payment routes (accessible to guests)
    Route::get('/payment/alignet/{payment}', [\App\Http\Controllers\PaymentController::class, 'showAlignetPaymentForm'])
        ->middleware(['alignet.cors'])
        ->name('payment.alignet');
    Route::get('/payment/alignet/query/{operationNumber}', [\App\Http\Controllers\PaymentController::class, 'queryAlignetTransaction'])
        ->middleware('throttle:10,1')
        ->name('payment.alignet.query');

    // Alignet Webhook Callback (Server-to-Server or Browser Post/Redirect)
    Route::any('/webhooks/payment/alignet', [\App\Http\Controllers\Webhooks\PaymentWebhookController::class, 'alignet'])
        ->middleware(['alignet.cors', 'throttle:60,1']) // 🔓 Allow CORS from Alignet | Rate Limit: 60 req/min
        ->name('webhooks.payment.alignet');

    // Alignet test/debug route (development only)

    // Booking Confirmation
    Route::get('/booking/{booking}/confirmation', [PublicBookingController::class, 'confirmation'])
        ->name('booking.confirmation');

    // Payment Status Polling (Breakout for Alignet Modal)
    Route::get('/api/payment/check-status/{payment}', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])
        ->name('api.payment.status');
    Route::get('/test-alignet', function () {
        $service = new \App\Services\AlignetPaymentService();

        $testData = $service->preparePaymentData(
            '999999999', // operation number
            100.00, // $100
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'address' => 'San Carlos',
                'city' => 'La Fortuna',
                'state' => 'Alajuela',
                'zip' => '21007',
                'country' => 'CR',
                'phone' => '+50624791471',
                'description' => 'Test transaction',
            ]
        );

        return view('test-alignet', ['paymentData' => $testData]);
    })->middleware('auth');

    // Alignet configuration test endpoint
    Route::get('/test/alignet-config', function () {
        $config = config('payment.gateways.alignet');
        $environment = $config['environment'] ?? 'testing';

        return response()->json([
            'commerce_id' => $config['commerce_id'] ?? null,
            'acquirer_id' => $config['acquirer_id'] ?? null,
            'base_url' => $config['urls'][$environment]['base'] ?? null,
            'vpos2_script' => $config['urls'][$environment]['vpos2_script'] ?? null,
            'secret_key_length' => strlen($config['secret_key'] ?? ''),
            'secret_key_preview' => substr($config['secret_key'] ?? '', 0, 10) . '...',
            'webhook_url' => route('webhooks.payment.alignet'),
            'environment' => $environment,
            'app_environment' => app()->environment(),
            'enabled' => $config['enabled'] ?? false,
        ]);
    })->middleware('auth')->name('test.alignet.config');


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
            Route::post('/profile/edit', [ProfileController::class, 'adminUpdate'])
                ->middleware('throttle:sensitive')
                ->name('profile.update');

            // ============================
            // DEBUG: EMAIL TEMPLATES
            // ============================
            Route::prefix('debug/mail')->name('debug.mail.')->group(function () {
                $resolveBooking = function (?int $id = null): Booking {
                    // Eager load ALL relationships needed by mail templates to avoid errors
                    $relations = [
                        'user',
                        'tour',
                        'tourLanguage',
                        'hotel',
                        'details.tour',
                        'details.hotel',
                        'details.schedule',
                        'details.tourLanguage',
                        'details.meetingPoint',
                        'details.meetingPoint.translations',
                        'redemption.promoCode',
                    ];

                    return $id
                        ? Booking::with($relations)->findOrFail($id)
                        : Booking::with($relations)->latest('booking_id')->firstOrFail();
                };

                // Confirmed
                Route::get('/confirmed/{id?}', function (?int $id = null) use ($resolveBooking) {
                    $booking = $resolveBooking($id);
                    $mailable = new BookingConfirmedMail($booking);
                    if ($lang = request('lang')) $mailable->locale($lang);
                    return $mailable->render();
                })->name('confirmed');

                // Cancelled
                Route::get('/cancelled/{id?}', function (?int $id = null) use ($resolveBooking) {
                    $booking = $resolveBooking($id);
                    $mailable = new BookingCancelledMail($booking);
                    if ($lang = request('lang')) $mailable->locale($lang);
                    return $mailable->render();
                })->name('cancelled');

                // Created (Admin Notification)
                Route::get('/created/{id?}', function (?int $id = null) use ($resolveBooking) {
                    $booking = $resolveBooking($id);
                    $mailable = new BookingCreatedMail($booking);
                    if ($lang = request('lang')) $mailable->locale($lang);
                    return $mailable->render();
                })->name('created');

                // Updated
                Route::get('/updated/{id?}', function (?int $id = null) use ($resolveBooking) {
                    $booking = $resolveBooking($id);
                    $mailable = new BookingUpdatedMail($booking);
                    if ($lang = request('lang')) $mailable->locale($lang);
                    return $mailable->render();
                })->name('updated');

                // ── Fortify / Auth Notifications ──

                $resolveUser = function (?int $id = null): \App\Models\User {
                    return $id
                        ? \App\Models\User::findOrFail($id)
                        : \App\Models\User::firstOrFail(); // Toma el primero si no hay ID expecífico
                };

                // Helper to set locale for preview
                $setPreviewLocale = function () {
                    if (request()->has('lang')) {
                        app()->setLocale(request('lang'));
                    }
                };

                // 1. Verify Email
                Route::get('/verify-email/{id?}', function (?int $id = null) use ($resolveUser, $setPreviewLocale) {
                    $setPreviewLocale();
                    $user = $resolveUser($id);
                    $notification = new \Illuminate\Auth\Notifications\VerifyEmail;
                    return $notification->toMail($user);
                })->name('verify-email');

                // 2. Reset Password
                Route::get('/reset-password/{id?}', function (?int $id = null) use ($resolveUser, $setPreviewLocale) {
                    $setPreviewLocale();
                    $user = $resolveUser($id);
                    $token = 'dummy-token-123';
                    $notification = new \App\Notifications\ResetPasswordNotification($token);
                    return $notification->toMail($user);
                })->name('reset-password');

                // 3. Password Updated (Success)
                Route::get('/password-updated/{id?}', function (?int $id = null) use ($resolveUser, $setPreviewLocale) {
                    $setPreviewLocale();
                    $user = $resolveUser($id);
                    $notification = new \App\Notifications\PasswordUpdatedNotification;
                    return $notification->toMail($user);
                })->name('password-updated');

                // 4. Email Change Request (Notification with token)
                Route::get('/email-change/{id?}', function (?int $id = null) use ($resolveUser, $setPreviewLocale) {
                    $setPreviewLocale();
                    $user = $resolveUser($id);
                    $token = 'dummy-token-123';
                    $notification = new \App\Notifications\EmailChangeVerificationNotification($token, app()->getLocale());
                    return $notification->toMail($user);
                })->name('email-change');

                // 5. Email Change Completed (Success)
                Route::get('/email-change-success/{id?}', function (?int $id = null) use ($resolveUser, $setPreviewLocale) {
                    $setPreviewLocale();
                    $user = $resolveUser($id);
                    $notification = new \App\Notifications\EmailChangeCompletedNotification;
                    return $notification->toMail($user);
                })->name('email-change-success');

                // 3. Account Locked
                Route::get('/account-locked/{id?}', function (?int $id = null) use ($resolveUser) {
                    $user = $resolveUser($id);
                    $unlockUrl = route('unlock.process', ['user' => $user->user_id, 'hash' => sha1($user->email)]);
                    $notification = new \App\Notifications\AccountLockedNotification($unlockUrl);
                    return $notification->toMail($user);
                })->name('account-locked');

                // 4. Email Change Verification
                Route::get('/email-change/{id?}', function (?int $id = null) use ($resolveUser) {
                    $user = $resolveUser($id);
                    $token = 'debug-email-change-token';
                    $locale = request('lang'); // Opcional override
                    $notification = new \App\Notifications\EmailChangeVerificationNotification($token, $locale);
                    return $notification->toMail($user);
                })->name('email-change');
            });

            // ============================
            // EMAIL PREVIEW SYSTEM
            // ============================
            Route::prefix('email-preview')->name('email-preview.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\EmailPreviewController::class, 'index'])->name('index');
                Route::get('/{type}', [\App\Http\Controllers\Admin\EmailPreviewController::class, 'show'])->name('show');
            });

            // ============================
            // 2FA PROTECTED ROUTES
            // ============================
            Route::middleware('2fa.admin')->group(function () {

                // Dashboard

                Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

                // ============================
                // USERS & ROLES
                // ============================
                Route::middleware(['can:view-users'])->group(function () {
                    Route::resource('users', UserRegisterController::class)->except(['show']);
                    Route::patch('users/{user}/lock', [UserRegisterController::class, 'lock'])->name('users.lock');
                    Route::patch('users/{user}/unlock', [UserRegisterController::class, 'unlock'])->name('users.unlock');
                    Route::patch('users/{user}/mark-verified', [UserRegisterController::class, 'markVerified'])->name('users.markVerified');
                    Route::patch('users/{user}/disable-2fa', [UserRegisterController::class, 'disable2FA'])->name('users.disable2FA');
                    // Soft delete routes
                    Route::get('users/trashed/list', [UserRegisterController::class, 'trashed'])->name('users.trashed');
                    Route::patch('users/{id}/restore', [UserRegisterController::class, 'restore'])->name('users.restore');
                    Route::delete('users/{id}/force-delete', [UserRegisterController::class, 'forceDelete'])->name('users.forceDelete');
                });

                Route::middleware(['can:view-roles'])->group(function () {
                    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
                    Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
                    Route::resource('roles', RoleController::class)->except(['show', 'create']);
                    Route::patch('roles/{role}/toggle', [RoleController::class, 'toggle'])->name('roles.toggle');
                });

                // ============================
                // CUSTOMER CATEGORIES
                // ============================
                Route::middleware(['can:view-customer-categories'])->group(function () {
                    Route::resource('customer_categories', CustomerCategoryController::class)
                        ->parameters(['customer_categories' => 'category']);
                    Route::post('customer_categories/{category}/toggle', [CustomerCategoryController::class, 'toggle'])
                        ->name('customer_categories.toggle');

                    // Soft Delete Routes (Customer Categories)
                    Route::get('customer_categories/trash/list', [CustomerCategoryController::class, 'trash'])->name('customer_categories.trash');
                    Route::patch('customer_categories/{category}/restore', [CustomerCategoryController::class, 'restore'])->name('customer_categories.restore');
                    Route::delete('customer_categories/{category}/force', [CustomerCategoryController::class, 'forceDelete'])->name('customer_categories.forceDelete');
                });

                // ============================
                // TAXES
                // ============================
                Route::middleware(['can:view-taxes'])->group(function () {
                    Route::resource('taxes', TaxController::class);
                    Route::post('taxes/{tax}/toggle', [TaxController::class, 'toggle'])->name('taxes.toggle');
                });

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
                Route::middleware(['can:view-tours'])->prefix('tours')->name('tours.')->group(function () {

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
                    Route::post('/', [TourController::class, 'store'])
                        ->middleware('throttle:sensitive')
                        ->name('store');
                    Route::get('/{tour}/edit', [TourWizardController::class, 'edit'])->name('edit');
                    Route::put('/{tour}', [TourController::class, 'update'])
                        ->middleware('throttle:sensitive')
                        ->name('update');
                    Route::patch('/{tour}/toggle', [TourController::class, 'toggle'])->name('toggle');
                    Route::delete('/{tour}', [TourController::class, 'destroy'])->name('destroy');

                    // Soft Delete Routes
                    Route::get('/trash/list', [TourController::class, 'trash'])->name('trash');
                    Route::patch('/{tour}/restore', [TourController::class, 'restore'])->name('restore');
                    Route::delete('/{tour}/force', [TourController::class, 'forceDelete'])->name('forceDelete');

                    // Extras moved from bottom group
                    Route::post('/{tour}/duplicate', [TourController::class, 'duplicate'])
                        ->middleware(['can:create-tours', 'throttle:sensitive'])
                        ->name('duplicate');
                    Route::get('/export/excel', [TourController::class, 'exportExcel'])->name('export.excel');

                    /**
                     * ============================================================
                     * TOUR WIZARD - Creación paso a paso con gestión de drafts
                     * ============================================================
                     */
                    Route::prefix('wizard')->name('wizard.')->group(function () {
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
                            ->middleware('throttle:sensitive')
                            ->name('store.details');

                        Route::post('/{tour}/update-details', [TourWizardController::class, 'updateDetails'])
                            ->middleware('throttle:sensitive')
                            ->name('update.details');

                        // Navegación entre pasos
                        Route::get('/{tour}/step/{step}', [TourWizardController::class, 'showStep'])
                            ->name('step');

                        // Paso 2: Itinerario
                        Route::post('/{tour}/store-itinerary', [TourWizardController::class, 'storeItinerary'])
                            ->middleware('throttle:sensitive')
                            ->name('store.itinerary');

                        // Paso 3: Horarios
                        Route::post('/{tour}/store-schedules', [TourWizardController::class, 'storeSchedules'])
                            ->middleware('throttle:sensitive')
                            ->name('store.schedules');
                        Route::post('/{tour}/quick-schedule', [TourWizardController::class, 'quickStoreSchedule'])
                            ->middleware('throttle:sensitive')
                            ->name('quick.schedule');

                        // Paso 4: Amenidades
                        Route::post('/{tour}/store-amenities', [TourWizardController::class, 'storeAmenities'])
                            ->middleware('throttle:sensitive')
                            ->name('store.amenities');
                        Route::post('/quick-amenity', [TourWizardController::class, 'quickStoreAmenity'])
                            ->middleware('throttle:sensitive')
                            ->name('quick.amenity');

                        // Paso 5: Precios
                        Route::post('/{tour}/store-prices', [TourWizardController::class, 'storePrices'])
                            ->middleware('throttle:sensitive')
                            ->name('store.prices');
                        Route::post('/quick-category', [TourWizardController::class, 'quickStoreCategory'])
                            ->middleware('throttle:sensitive')
                            ->name('quick.category');

                        // Paso 6: Publicar
                        Route::post('/{tour}/publish', [TourWizardController::class, 'publish'])
                            ->middleware('throttle:sensitive')
                            ->name('publish');

                        // Quick creates (AJAX)
                        Route::post('/quick-tour-type', [TourWizardController::class, 'quickStoreTourType'])
                            ->middleware('throttle:sensitive')
                            ->name('quick.tour-type');
                        Route::post('/quick-language', [TourWizardController::class, 'quickStoreLanguage'])
                            ->middleware('throttle:sensitive')
                            ->name('quick.language');
                        Route::post('/quick-itinerary-item', [TourWizardController::class, 'quickCreateItineraryItem'])
                            ->middleware('throttle:sensitive')
                            ->name('quick.itinerary-item');
                    });

                    /**
                     * ============================================================
                     * 🆕 AUDITORÍA
                     * ============================================================
                     */
                    Route::prefix('audit')->name('audit.')->middleware('can:view-audit')->group(function () {
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
                        Route::get('/drafts', [TourController::class, 'draftsStats'])
                            ->name('drafts');
                        Route::get('/users', [TourController::class, 'usersStats'])
                            ->name('users');
                        Route::get('/activity', [TourController::class, 'activityStats'])
                            ->name('activity');
                    });

                    // -------------------- TOUR ORDER --------------------
                    Route::get('/order', [TourOrderController::class, 'index'])->name('order.index')->middleware('can:reorder-tours');
                    Route::post('/order/{tourType}/save', [TourOrderController::class, 'save'])->name('order.save')->middleware('can:reorder-tours');

                    // -------------------- PRICES (por categoría) --------------------
                    Route::prefix('{tour}/prices')->name('prices.')->group(function () {
                        Route::get('/', [TourPriceController::class, 'index'])->name('index');
                        Route::post('/', [TourPriceController::class, 'store'])
                            ->middleware('throttle:sensitive')
                            ->name('store');
                        Route::post('/bulk-update', [TourPriceController::class, 'bulkUpdate'])
                            ->middleware('throttle:sensitive')
                            ->name('bulk-update');
                        Route::put('/{price}', [TourPriceController::class, 'update'])
                            ->middleware('throttle:sensitive')
                            ->name('update');
                        Route::post('/{price}/toggle', [TourPriceController::class, 'toggle'])->name('toggle');
                        Route::delete('/{price}', [TourPriceController::class, 'destroy'])->name('destroy');
                        Route::post('/update-taxes', [TourPriceController::class, 'updateTaxes'])
                            ->middleware('throttle:sensitive')
                            ->name('update-taxes');
                    });

                    // -------------------- SCHEDULES (Horarios) --------------------
                    Route::prefix('schedule')->name('schedule.')->group(function () {
                        // Soft Delete Routes (Schedules) - Moved to top to avoid parameter collision
                        Route::get('/trash/list', [TourScheduleController::class, 'trash'])->name('trash');
                        Route::patch('/{id}/restore', [TourScheduleController::class, 'restore'])->name('restore');
                        Route::delete('/{id}/force', [TourScheduleController::class, 'forceDelete'])->name('forceDelete');

                        Route::get('/', [TourScheduleController::class, 'index'])->name('index');
                        Route::post('/', [TourScheduleController::class, 'store'])
                            ->middleware('throttle:sensitive')
                            ->name('store');
                        Route::put('/{schedule}', [TourScheduleController::class, 'update'])
                            ->middleware('throttle:sensitive')
                            ->name('update');
                        Route::delete('/{schedule}', [TourScheduleController::class, 'destroy'])->name('destroy');
                        Route::put('/{schedule}/toggle', [TourScheduleController::class, 'toggle'])->name('toggle');

                        // Asignación a tours
                        Route::post('/{tour}/attach', [TourScheduleController::class, 'attach'])
                            ->middleware('throttle:sensitive')
                            ->name('attach');
                        Route::delete('/{tour}/{schedule}/detach', [TourScheduleController::class, 'detach'])->name('detach');
                        Route::patch('/{tour}/{schedule}/assignment-toggle', [TourScheduleController::class, 'toggleAssignment'])->name('assignment.toggle');

                        // 🆕 ACTUALIZAR CAPACIDAD DEL PIVOTE (tour+schedule)
                        Route::patch('/{tour}/{schedule}/pivot', [TourScheduleController::class, 'updatePivotCapacity'])
                            ->middleware('throttle:sensitive')
                            ->name('update-pivot-capacity');
                    });

                    // -------------------- CAPACITY MANAGEMENT --------------------
                    Route::group(['middleware' => ['can:view-tour-availability']], function () {
                        Route::prefix('capacity')->name('capacity.')->group(function () {
                            // Vista principal con tabs
                            Route::get('/', [TourAvailabilityController::class, 'index'])->name('index');

                            // Capacidad GLOBAL del tour
                            Route::patch('/tour/{tour}', [TourAvailabilityController::class, 'updateTourCapacity'])
                                ->middleware('throttle:sensitive')
                                ->name('update-tour');

                            // Capacidad BASE por horario (pivot)
                            Route::patch('/tour/{tour}/schedule/base-capacity', [TourAvailabilityController::class, 'updateScheduleBaseCapacity'])
                                ->middleware('throttle:sensitive')
                                ->name('update-schedule-base');

                            // Override puntual por día+horario
                            Route::post('/tour/{tour}/overrides/day-schedule', [TourAvailabilityController::class, 'upsertDayScheduleOverride'])
                                ->middleware('throttle:sensitive')
                                ->name('override-day-schedule');

                            // Bloqueo puntual por día+horario
                            Route::post('/tour/{tour}/overrides/day-schedule/toggle-block', [TourAvailabilityController::class, 'toggleBlockDaySchedule'])
                                ->middleware('throttle:sensitive')
                                ->name('toggle-block-day-schedule');

                            // ===== WIDGET DE ALERTAS (CapacityController) =====
                            Route::prefix('schedules/{schedule}')->group(function () {
                                Route::patch('/increase', [CapacityController::class, 'increase'])
                                    ->middleware('throttle:sensitive')
                                    ->name('increase');
                                Route::get('/details', [CapacityController::class, 'show'])->name('details');
                                Route::patch('/block', [CapacityController::class, 'block'])
                                    ->middleware('throttle:sensitive')
                                    ->name('block');
                            });

                            // Legacy CRUD directo
                            Route::post('/', [TourAvailabilityController::class, 'store'])
                                ->middleware('throttle:sensitive')
                                ->name('store');
                            Route::patch('/{availability}', [TourAvailabilityController::class, 'update'])
                                ->middleware('throttle:sensitive')
                                ->name('update');
                            Route::delete('/{availability}', [TourAvailabilityController::class, 'destroy'])->name('destroy');
                        });
                    });

                    // -------------------- ITINERARY --------------------
                    Route::resource('itinerary', ItineraryController::class)->except(['show']);
                    Route::get('itinerary/trash/list', [ItineraryController::class, 'trash'])->name('itinerary.trash');
                    Route::patch('itineraries/{itinerary}/toggle', [ItineraryController::class, 'toggle'])->name('itinerary.toggle');
                    Route::post('itinerary/{itinerary}/assign-items', [ItineraryController::class, 'assignItems'])
                        ->middleware('throttle:sensitive')
                        ->name('itinerary.assignItems');
                    Route::put('itinerary/{itinerary}/translations', [ItineraryController::class, 'updateTranslations'])
                        ->middleware('throttle:sensitive')
                        ->name('itinerary.updateTranslations');

                    // -------------------- ITINERARY ITEMS --------------------
                    Route::resource('itinerary_items', ItineraryItemController::class)->except(['show', 'create', 'edit']);
                    Route::patch('itinerary_items/{itinerary_item}/toggle', [ItineraryItemController::class, 'toggle'])->name('itinerary_items.toggle');
                    Route::put('itinerary_items/{itinerary_item}/translations', [ItineraryItemController::class, 'updateTranslations'])
                        ->middleware('throttle:sensitive')
                        ->name('itinerary_items.updateTranslations');
                });

                // ============================
                // TOURS (ADMIN) - IMAGES
                // ============================
                Route::group(['middleware' => ['can:manage-tour-images']], function () {
                    Route::prefix('tours')->name('tours.')->group(function () {
                        Route::get('images', [TourImageController::class, 'pick'])->name('images.pick');
                        Route::get('{tour}/images', [TourImageController::class, 'index'])->name('images.index');
                        Route::post('{tour}/images', [TourImageController::class, 'store'])
                            ->middleware('throttle:sensitive')
                            ->name('images.store');
                        Route::put('{tour}/images/reorder', [TourImageController::class, 'reorder'])
                            ->middleware('throttle:sensitive')
                            ->name('images.reorder');
                        Route::put('{tour}/images/{image}', [TourImageController::class, 'update'])
                            ->middleware('throttle:sensitive')
                            ->name('images.update');
                        Route::delete('{tour}/images/{image}', [TourImageController::class, 'destroy'])->name('images.destroy');
                        Route::post('{tour}/images/{image}/cover', [TourImageController::class, 'setCover'])->name('images.cover');
                        Route::post('{tour}/images/bulk-destroy', [TourImageController::class, 'bulkDestroy'])->name('images.bulk-destroy');
                        Route::delete('{tour}/images/destroy-all', [TourImageController::class, 'destroyAll'])->name('images.destroyAll');
                    });
                });

                // ============================
                // AMENITIES
                // ============================
                Route::group(['middleware' => ['can:view-amenities']], function () {
                    Route::prefix('tours')->name('tours.')->group(function () {
                        // Soft Delete Routes (Amenities)
                        Route::get('amenities/trash/list', [AmenityController::class, 'trash'])->name('amenities.trash');
                        Route::patch('amenities/{amenity}/restore', [AmenityController::class, 'restore'])->name('amenities.restore');
                        Route::delete('amenities/{amenity}/force', [AmenityController::class, 'forceDelete'])->name('amenities.forceDelete');

                        Route::resource('amenities', AmenityController::class)->except(['show']);
                        Route::patch('amenities/{amenity}/toggle', [AmenityController::class, 'toggle'])->name('amenities.toggle');
                    });
                });

                // ============================
                // TOUR AVAILABILITY (Excluded Dates)
                // ============================
                Route::group(['middleware' => ['can:view-tour-availability']], function () {
                    Route::prefix('tours')->name('tours.')->group(function () {
                        Route::prefix('excluded_dates')->name('excluded_dates.')->group(function () {
                            Route::get('/', [TourExcludedDateController::class, 'index'])->name('index');
                            Route::get('/blocked', [TourExcludedDateController::class, 'blocked'])->name('blocked');
                            Route::post('/', [TourExcludedDateController::class, 'store'])
                                ->middleware('throttle:sensitive')
                                ->name('store');
                            Route::put('/{excludedDate}', [TourExcludedDateController::class, 'update'])
                                ->middleware('throttle:sensitive')
                                ->name('update');
                            Route::delete('/{excludedDate}', [TourExcludedDateController::class, 'destroy'])->name('destroy');
                            Route::post('/toggle', [TourExcludedDateController::class, 'toggle'])->name('toggle');
                            Route::post('/bulk-toggle', [TourExcludedDateController::class, 'bulkToggle'])
                                ->middleware('throttle:sensitive')
                                ->name('bulkToggle');
                            Route::post('/block-all', [TourExcludedDateController::class, 'blockAll'])
                                ->middleware('throttle:sensitive')
                                ->name('blockAll');
                            Route::post('/store-multiple', [TourExcludedDateController::class, 'storeMultiple'])
                                ->middleware('throttle:sensitive')
                                ->name('storeMultiple');
                            Route::delete('/all', [TourExcludedDateController::class, 'destroyAll'])->name('destroyAll');
                            Route::post('/destroy-selected', [TourExcludedDateController::class, 'destroySelected'])->name('destroySelected');
                        });
                    });
                });

                // ============================
                // TOUR PRICING
                // ============================
                Route::group(['middleware' => ['can:view-tour-prices']], function () {
                    Route::prefix('tours')->name('tours.')->group(function () {
                        Route::post('prices/check-overlap', [TourPriceController::class, 'checkOverlap'])
                            ->name('prices.check-overlap');
                    });
                });

                // ============================
                // TOUR IMAGES (Unified Permission)
                // ============================
                Route::group(['middleware' => ['can:view-tour-images']], function () {
                    // Tour Images
                    Route::prefix('tours')->name('tours.')->group(function () {
                        Route::get('images', [TourImageController::class, 'pick'])->name('images.pick');
                        Route::get('{tour}/images', [TourImageController::class, 'index'])->name('images.index');
                        Route::post('{tour}/images', [TourImageController::class, 'store'])
                            ->middleware('throttle:sensitive')
                            ->name('images.store');
                        Route::put('{tour}/images/reorder', [TourImageController::class, 'reorder'])
                            ->middleware('throttle:sensitive')
                            ->name('images.reorder');
                        Route::put('{tour}/images/{image}', [TourImageController::class, 'update'])
                            ->middleware('throttle:sensitive')
                            ->name('images.update');
                        Route::delete('{tour}/images/{image}', [TourImageController::class, 'destroy'])->name('images.destroy');
                        Route::post('{tour}/images/{image}/cover', [TourImageController::class, 'setCover'])->name('images.setCover');
                        Route::post('{tour}/images/bulk-destroy', [TourImageController::class, 'bulkDestroy'])->name('images.bulk-destroy');
                        Route::delete('{tour}/images/destroy-all', [TourImageController::class, 'destroyAll'])->name('images.destroyAll');
                    });

                    // Tour Type Images
                    Route::prefix('types')->name('types.')->group(function () {
                        Route::get('images', [TourTypeCoverPickerController::class, 'pick'])->name('images.pick');
                        Route::get('images/{tourType}/edit', [TourTypeCoverPickerController::class, 'edit'])->name('images.edit');
                        Route::put('images/{tourType}', [TourTypeCoverPickerController::class, 'updateCover'])
                            ->middleware('throttle:sensitive')
                            ->name('images.update');
                    });
                });

                // ============================
                // TOUR TYPES
                // ============================
                Route::group(['middleware' => ['can:view-tour-types']], function () {
                    // Translation management routes (before resource)

                    Route::get('tourtypes/{tourType}/translations', [TourTypeController::class, 'editTranslations'])
                        ->name('tourtypes.translations.edit');
                    Route::put('tourtypes/{tourType}/translations/{locale}', [TourTypeController::class, 'updateTranslation'])
                        ->middleware('throttle:sensitive')
                        ->name('tourtypes.translations.update');

                    Route::resource('tourtypes', TourTypeController::class, ['parameters' => ['tourtypes' => 'tourType']])->except(['show']);

                    // Soft Delete Routes (Tour Types)
                    Route::get('tourtypes/trash/list', [TourTypeController::class, 'trash'])->name('tourtypes.trash');
                    Route::patch('tourtypes/{tourType}/restore', [TourTypeController::class, 'restore'])->name('tourtypes.restore');
                    Route::delete('tourtypes/{tourType}/force', [TourTypeController::class, 'forceDelete'])->name('tourtypes.forceDelete');

                    Route::put('tourtypes/{tourType}/toggle', [TourTypeController::class, 'toggle'])->name('tourtypes.toggle');
                });

                // ============================
                // LANGUAGES (Part of Settings mostly)
                // ============================
                Route::group(['middleware' => ['can:view-settings']], function () {

                    // Soft Delete Routes (Languages)
                    Route::get('languages/trash/list', [TourLanguageController::class, 'trash'])->name('languages.trash');
                    Route::patch('languages/{language}/restore', [TourLanguageController::class, 'restore'])->name('languages.restore');
                    Route::delete('languages/{language}/force', [TourLanguageController::class, 'forceDelete'])->name('languages.forceDelete');

                    Route::resource('languages', TourLanguageController::class, ['parameters' => ['languages' => 'language']])->except(['show']);
                    Route::patch('languages/{language}/toggle', [TourLanguageController::class, 'toggle'])->name('languages.toggle');
                });

                // ============================
                // HOTELS
                // ============================
                Route::group(['middleware' => ['can:view-hotels']], function () {
                    Route::resource('hotels', HotelListController::class)->except(['show', 'create', 'edit']);
                    Route::post('hotels/sort', [HotelListController::class, 'sort'])
                        ->middleware('throttle:sensitive')
                        ->name('hotels.sort');
                    Route::patch('hotels/{hotel}/toggle', [HotelListController::class, 'toggle'])->name('hotels.toggle');
                });

                // ============================
                // MEETING POINTS
                // ============================
                Route::group(['middleware' => ['can:view-meeting-points']], function () {
                    Route::resource('meetingpoints', MeetingPointSimpleController::class)->except(['show', 'create', 'edit']);
                    Route::patch('meetingpoints/{meetingpoint}/toggle', [MeetingPointSimpleController::class, 'toggle'])->name('meetingpoints.toggle');

                    // Trash management routes
                    Route::get('meetingpoints/trash/list', [MeetingPointSimpleController::class, 'trash'])->name('meetingpoints.trash');
                    Route::patch('meetingpoints/{id}/restore', [MeetingPointSimpleController::class, 'restore'])->name('meetingpoints.restore');
                    Route::delete('meetingpoints/{id}/force', [MeetingPointSimpleController::class, 'forceDelete'])->name('meetingpoints.forceDelete');
                });

                // ============================
                // BOOKINGS (ADMIN)
                // ============================
                Route::group(['middleware' => ['can:view-bookings']], function () {
                    Route::prefix('bookings')
                        ->name('bookings.')
                        ->controller(AdminBookingController::class)
                        ->group(function () {
                            // Export
                            Route::get('export/excel', 'exportExcel')->name('export.excel');
                            Route::get('export/pdf', 'exportPdf')->name('export.pdf');

                            // Calendar
                            Route::get('reserved', 'reservedSeats')->name('reserved');

                            // Validate Capacity (AJAX)
                            Route::post('validate-capacity', 'validateCapacity')->name('validate_capacity');

                            // Payment Link
                            Route::post('{booking}/payment-link', 'generatePaymentLink')
                                ->middleware('throttle:sensitive')
                                ->name('payment_link');
                            Route::post('{booking}/regenerate-payment-link', 'regeneratePaymentLink')
                                ->middleware('throttle:sensitive')
                                ->name('regenerate_payment_link');

                            // Unpaid Bookings Management (Pay-Later System)
                            Route::get('unpaid', 'unpaidIndex')->name('unpaid');
                            Route::post('{booking}/extend', 'extendBooking')
                                ->middleware('throttle:sensitive')
                                ->name('extend');
                            Route::post('{booking}/cancel-unpaid', 'cancelUnpaid')
                                ->middleware('throttle:sensitive')
                                ->name('cancel_unpaid');
                            Route::get('calendar-data', 'calendarData')->name('calendarData');
                            Route::get('calendar', 'calendar')->name('calendar');

                            // API promo verification
                            Route::get('verify-promo-code', 'verifyPromoCode')->name('verifyPromoCode');

                            // CRUD principal
                            Route::get('', 'index')->name('index');
                            Route::get('create', 'create')->name('create');
                            Route::post('', 'store')
                                ->middleware('throttle:sensitive')
                                ->name('store');
                            Route::post('from-cart', 'storeFromCart')
                                ->middleware('throttle:sensitive')
                                ->name('storeFromCart');
                            Route::get('{booking}/edit', 'edit')->name('edit');
                            Route::match(['put', 'patch'], '{booking}', 'update')
                                ->middleware('throttle:sensitive')
                                ->name('update');
                            Route::delete('{booking}', 'destroy')->name('destroy');

                            // NUEVO: detalle (show)
                            Route::get('{booking}', 'show')->name('show');

                            // SoftDelete actions
                            Route::post('{id}/restore', 'restore')->name('restore');
                            Route::delete('{id}/force', 'forceDelete')->name('forceDelete');

                            // Estado y recibo
                            Route::patch('{booking}/status', 'updateStatus')
                                ->middleware('throttle:sensitive')
                                ->name('update-status');
                            Route::get('{booking}/receipt', 'generateReceipt')->name('receipt');
                        });
                });

                // ============================
                // PAYMENTS (ADMIN)
                // ============================
                Route::group(['middleware' => ['can:view-payments']], function () {
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
                            Route::post('{payment}/refund', 'refund')
                                ->middleware('throttle:sensitive')
                                ->name('refund');
                        });
                });

                // ============================
                // CART (ADMIN)
                // ============================
                Route::group(['middleware' => ['can:view-carts']], function () {
                    Route::prefix('carts')->name('carts.')->group(function () {
                        Route::get('/', [AdminCartController::class, 'index'])->name('index');
                        Route::post('/', [AdminCartController::class, 'store'])
                            ->middleware('throttle:cart')
                            ->name('store');
                        Route::patch('/{item}', [AdminCartController::class, 'update'])
                            ->middleware('throttle:cart')
                            ->name('update');
                        Route::delete('/item/{item}', [AdminCartController::class, 'destroy'])->name('item.destroy');

                        Route::get('/all', [AdminCartController::class, 'allCarts'])->name('all');
                        Route::delete('/{cart}', [AdminCartController::class, 'destroyCart'])->name('destroy');
                        Route::patch('/{cart}/toggle', [AdminCartController::class, 'toggleActive'])->name('toggle');

                        Route::post('/apply-promo', [AdminCartController::class, 'applyPromoAdmin'])
                            ->middleware('throttle:promo')
                            ->name('applyPromo');
                        Route::delete('/remove-promo', [AdminCartController::class, 'removePromoAdmin'])
                            ->middleware('throttle:promo')
                            ->name('removePromo');
                    });
                });

                // ============================
                // PROMO CODES
                // ============================
                Route::group(['middleware' => ['can:view-promo-codes']], function () {
                    Route::prefix('promo-codes')->name('promoCodes.')->group(function () {
                        Route::get('/', [PromoCodeController::class, 'index'])->name('index');
                        Route::post('/', [PromoCodeController::class, 'store'])
                            ->middleware('throttle:sensitive')
                            ->name('store');
                        Route::delete('/{promo}', [PromoCodeController::class, 'destroy'])->name('destroy');
                        Route::patch('/{promo}/operation', [PromoCodeController::class, 'updateOperation'])
                            ->middleware('throttle:sensitive')
                            ->name('updateOperation');
                    });
                });

                // ============================
                // SETTINGS
                // ============================
                Route::middleware(['can:view-settings'])->group(function () {
                    Route::prefix('settings')->name('settings.')->group(function () {
                        Route::get('/', [SettingsController::class, 'index'])->name('index');
                        Route::post('/', [SettingsController::class, 'update'])
                            ->middleware('throttle:sensitive')
                            ->name('update');
                    });
                });

                // ============================
                // REVIEWS MANAGEMENT
                // ============================
                // Providers
                Route::group(['middleware' => ['can:view-review-providers']], function () {
                    Route::resource('review-providers', ReviewProviderController::class)
                        ->except(['show'])
                        ->parameters(['review-providers' => 'provider'])
                        ->names('review-providers');
                    Route::post('review-providers/{provider}/toggle', [ReviewProviderController::class, 'toggle'])->name('review-providers.toggle');
                    Route::post('review-providers/{provider}/test', [ReviewProviderController::class, 'test'])->name('review-providers.test');
                    Route::post('review-providers/{provider}/cache/flush', [ReviewProviderController::class, 'flushCache'])->name('review-providers.flush');

                    // Product Mapping
                    Route::get('review-providers/{provider}/product-map', [\App\Http\Controllers\Admin\Reviews\ProductMappingController::class, 'index'])->name('review-providers.product-map.index');
                    Route::post('review-providers/{provider}/product-map', [\App\Http\Controllers\Admin\Reviews\ProductMappingController::class, 'store'])->name('review-providers.product-map.store');
                    Route::put('review-providers/{provider}/product-map/{tourId}', [\App\Http\Controllers\Admin\Reviews\ProductMappingController::class, 'update'])->name('review-providers.product-map.update');
                    Route::delete('review-providers/{provider}/product-map/{tourId}', [\App\Http\Controllers\Admin\Reviews\ProductMappingController::class, 'destroy'])->name('review-providers.product-map.destroy');
                });


                // Reviews
                Route::group(['middleware' => ['can:view-reviews']], function () {
                    Route::prefix('reviews')->name('reviews.')->group(function () {
                        Route::get('/', [ReviewAdminController::class, 'index'])->name('index');
                        Route::get('/create', [ReviewAdminController::class, 'create'])->name('create');
                        Route::post('/', [ReviewAdminController::class, 'store'])
                            ->middleware('throttle:sensitive')
                            ->name('store');
                        Route::get('/{review}/edit', [ReviewAdminController::class, 'edit'])->name('edit');
                        Route::put('/{review}', [ReviewAdminController::class, 'update'])
                            ->middleware('throttle:sensitive')
                            ->name('update');
                        Route::delete('/{review}', [ReviewAdminController::class, 'destroy'])->name('destroy');

                        Route::post('/{review}/publish', [ReviewAdminController::class, 'publish'])->name('publish');
                        Route::post('/{review}/hide', [ReviewAdminController::class, 'hide'])->name('hide');
                        Route::post('/{review}/flag', [ReviewAdminController::class, 'flag'])->name('flag');
                        Route::post('/bulk', [ReviewAdminController::class, 'bulk'])
                            ->middleware('throttle:sensitive')
                            ->name('bulk');

                        // Replies & Threads
                        Route::get('/{review}/replies/create', [ReviewReplyController::class, 'create'])->name('replies.create');
                        Route::post('/{review}/replies', [ReviewReplyController::class, 'store'])
                            ->middleware('throttle:sensitive')
                            ->name('replies.store');
                        Route::delete('/{review}/replies/{reply}', [ReviewReplyController::class, 'destroy'])->name('replies.destroy');
                        Route::post('/{review}/replies/{reply}/toggle', [ReviewReplyController::class, 'toggle'])->name('replies.toggle');
                        Route::get('/{review}/thread', [ReviewReplyController::class, 'thread'])->name('replies.thread');
                    });
                });

                // Review Requests
                Route::group(['middleware' => ['can:view-review-requests']], function () {
                    Route::prefix('review-requests')->name('review-requests.')->group(function () {
                        Route::get('/', [ReviewRequestAdminController::class, 'index'])->name('index');
                        Route::post('/{booking}/send', [ReviewRequestAdminController::class, 'send'])
                            ->middleware('throttle:email')
                            ->name('send');
                        Route::post('/{rr}/resend', [ReviewRequestAdminController::class, 'resend'])
                            ->middleware('throttle:email')
                            ->name('resend');
                        Route::delete('/{rr}', [ReviewRequestAdminController::class, 'destroy'])->name('destroy');
                    });
                });

                // ============================
                // FAQs
                // ============================
                Route::group(['middleware' => ['can:view-faqs']], function () {
                    // Soft Delete Routes (FAQs) - Must be before resource to avoid parameter collision
                    Route::get('faqs/trash/list', [AdminFaqController::class, 'trash'])->name('faqs.trash');
                    Route::patch('faqs/{id}/restore', [AdminFaqController::class, 'restore'])->name('faqs.restore');
                    Route::delete('faqs/{id}/force', [AdminFaqController::class, 'forceDelete'])->name('faqs.forceDelete');

                    Route::resource('faqs', AdminFaqController::class)->except(['show']);
                    Route::patch('faqs/{faq}/toggle-status', [AdminFaqController::class, 'toggleStatus'])->name('faqs.toggleStatus');
                });

                // ============================
                // POLICIES
                // ============================
                Route::group(['middleware' => ['can:view-policies']], function () {
                    Route::prefix('policies')->name('policies.')->group(function () {
                        Route::get('/', [PolicyController::class, 'index'])->name('index');
                        Route::post('/', [PolicyController::class, 'store'])
                            ->middleware('throttle:sensitive')
                            ->name('store');
                        Route::put('/{policy:policy_id}', [PolicyController::class, 'update'])
                            ->middleware('throttle:sensitive')
                            ->name('update');
                        Route::post('/{policy:policy_id}/toggle', [PolicyController::class, 'toggle'])->name('toggle');
                        Route::delete('/{policy:policy_id}', [PolicyController::class, 'destroy'])->name('destroy');
                        Route::post('/{policy:policy_id}/restore', [PolicyController::class, 'restore'])->name('restore');
                        Route::delete('/{policy:policy_id}/force', [PolicyController::class, 'forceDestroy'])
                            ->middleware('can:policies.forceDelete')
                            ->name('forceDestroy');

                        // Policy Sections
                        Route::group(['middleware' => ['can:view-policy-sections']], function () {
                            Route::get('/{policy:policy_id}/sections', [PolicySectionController::class, 'index'])->name('sections.index');
                            Route::post('/{policy:policy_id}/sections', [PolicySectionController::class, 'store'])
                                ->middleware('throttle:sensitive')
                                ->name('sections.store');
                            Route::put('/{policy:policy_id}/sections/{section}', [PolicySectionController::class, 'update'])
                                ->middleware('throttle:sensitive')
                                ->name('sections.update');
                            Route::post('/{policy:policy_id}/sections/{section}/toggle', [PolicySectionController::class, 'toggle'])->name('sections.toggle');
                            Route::delete('/{policy:policy_id}/sections/{section}', [PolicySectionController::class, 'destroy'])->name('sections.destroy');

                            // Restore & Force Delete
                            Route::post('/{policy:policy_id}/sections/{section}/restore', [PolicySectionController::class, 'restore'])->name('sections.restore');
                            Route::delete('/{policy:policy_id}/sections/{section}/force', [PolicySectionController::class, 'forceDestroy'])->name('sections.forceDestroy');
                        });
                    });
                });

                // ============================
                // TRANSLATIONS
                // ============================
                Route::group(['middleware' => ['can:view-translations']], function () {
                    Route::prefix('translations')->name('translations.')->group(function () {
                        Route::get('/', [TranslationController::class, 'index'])->name('index');
                        Route::get('/{type}/choose-locale', [TranslationController::class, 'chooseLocale'])->name('choose-locale');
                        Route::get('/{type}/select', [TranslationController::class, 'select'])->name('select');
                        Route::get('/{type}/{id}/edit', [TranslationController::class, 'edit'])->name('edit');
                        Route::post('/{type}/{id}/update', [TranslationController::class, 'update'])
                            ->middleware('throttle:sensitive')
                            ->name('update');
                        Route::post('/change-editing-locale', [TranslationController::class, 'changeEditingLocale'])->name('change-editing-locale');
                    });
                });

                // ============================
                // EMAIL TEMPLATES - DEPRECATED (Moved to Blade templates)
                // ============================
                /*
                Route::group(['middleware' => ['can:view-email-templates']], function () {
                    Route::prefix('email-templates')->name('email-templates.')->group(function () {
                        Route::get('/', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('index');
                        Route::get('/{template}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])
                            ->middleware('can:edit-email-templates')
                            ->name('edit');
                        Route::put('/{template}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])
                            ->middleware(['can:edit-email-templates', 'throttle:sensitive'])
                            ->name('update');
                        Route::post('/{template}/toggle', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'toggle'])
                            ->middleware('can:edit-email-templates')
                            ->name('toggle');
                        Route::get('/{template}/preview', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])
                            ->name('preview');
                    });
                });
                */

                // ============================
                // REPORTS
                // ============================
                Route::group(['middleware' => ['can:view-reports']], function () {
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

                        // 📊 NEW DASHBOARDS
                        // Sales Dashboard
                        Route::get('/sales', [\App\Http\Controllers\Admin\Reports\SalesReportController::class, 'index'])->name('sales');
                        Route::get('/sales/chart/revenue-trend', [\App\Http\Controllers\Admin\Reports\SalesReportController::class, 'chartRevenueTrend'])->name('sales.chart.revenue-trend');
                        Route::get('/sales/chart/payment-method', [\App\Http\Controllers\Admin\Reports\SalesReportController::class, 'chartRevenueByPaymentMethod'])->name('sales.chart.payment-method');
                        Route::get('/sales/chart/language', [\App\Http\Controllers\Admin\Reports\SalesReportController::class, 'chartRevenueByLanguage'])->name('sales.chart.language');
                        Route::get('/sales/chart/comparison', [\App\Http\Controllers\Admin\Reports\SalesReportController::class, 'chartDailyComparison'])->name('sales.chart.comparison');

                        // Tours Dashboard
                        Route::get('/tours', [\App\Http\Controllers\Admin\Reports\ToursReportController::class, 'index'])->name('tours');
                        Route::get('/tours/chart/top-revenue', [\App\Http\Controllers\Admin\Reports\ToursReportController::class, 'chartTopToursByRevenue'])->name('tours.chart.top-revenue');
                        Route::get('/tours/chart/top-bookings', [\App\Http\Controllers\Admin\Reports\ToursReportController::class, 'chartTopToursByBookings'])->name('tours.chart.top-bookings');
                        Route::get('/tours/chart/performance-matrix', [\App\Http\Controllers\Admin\Reports\ToursReportController::class, 'chartTourPerformanceMatrix'])->name('tours.chart.performance-matrix');
                        Route::get('/tours/chart/tour-type', [\App\Http\Controllers\Admin\Reports\ToursReportController::class, 'chartBookingsByTourType'])->name('tours.chart.tour-type');
                        Route::get('/tours/chart/capacity', [\App\Http\Controllers\Admin\Reports\ToursReportController::class, 'chartCapacityUtilization'])->name('tours.chart.capacity');

                        // Customer Analytics Dashboard
                        Route::get('/customers', [\App\Http\Controllers\Admin\Reports\CustomerReportController::class, 'index'])->name('customers');
                        Route::get('/customers/chart/geographic', [\App\Http\Controllers\Admin\Reports\CustomerReportController::class, 'chartGeographicDistribution'])->name('customers.chart.geographic');
                        Route::get('/customers/chart/top-countries', [\App\Http\Controllers\Admin\Reports\CustomerReportController::class, 'chartTopCountries'])->name('customers.chart.top-countries');
                        Route::get('/customers/chart/growth', [\App\Http\Controllers\Admin\Reports\CustomerReportController::class, 'chartCustomerGrowth'])->name('customers.chart.growth');
                        Route::get('/customers/chart/new-vs-returning', [\App\Http\Controllers\Admin\Reports\CustomerReportController::class, 'chartNewVsReturning'])->name('customers.chart.new-vs-returning');
                        Route::get('/customers/chart/cities', [\App\Http\Controllers\Admin\Reports\CustomerReportController::class, 'chartCustomersByCity'])->name('customers.chart.cities');

                        // Time Analysis Dashboard
                        Route::get('/time-analysis', [\App\Http\Controllers\Admin\Reports\TimeAnalysisController::class, 'index'])->name('time-analysis');
                        Route::get('/time-analysis/chart/day-of-week', [\App\Http\Controllers\Admin\Reports\TimeAnalysisController::class, 'chartBookingsByDayOfWeek'])->name('time-analysis.chart.day-of-week');
                        Route::get('/time-analysis/chart/hour', [\App\Http\Controllers\Admin\Reports\TimeAnalysisController::class, 'chartBookingsByHour'])->name('time-analysis.chart.hour');
                        Route::get('/time-analysis/chart/seasonality', [\App\Http\Controllers\Admin\Reports\TimeAnalysisController::class, 'chartMonthlySeasonality'])->name('time-analysis.chart.seasonality');
                        Route::get('/time-analysis/chart/heatmap', [\App\Http\Controllers\Admin\Reports\TimeAnalysisController::class, 'chartHeatmap'])->name('time-analysis.chart.heatmap');
                    });
                });
            });
        });
});

// ============================
// COOKIES (consent) - Outside admin middleware
// ============================
Route::post('/cookies/accept', [CookieConsentController::class, 'accept'])
    ->name('cookies.accept')
    ->middleware('throttle:preferences');

Route::post('/cookies/reject', [CookieConsentController::class, 'reject'])
    ->name('cookies.reject')
    ->middleware('throttle:preferences');

Route::post('/cookies/customize', [CookieConsentController::class, 'customize'])
    ->name('cookies.customize')
    ->middleware('throttle:preferences');

Route::get('/cookies/preferences', [CookieConsentController::class, 'getPreferences'])
    ->name('cookies.preferences');


// ============================
// PASSWORD SETUP (Guest to Registered User)
// ============================
use App\Http\Controllers\Auth\PasswordSetupController;

Route::middleware('guest')->group(function () {
    Route::get('/account/setup', [PasswordSetupController::class, 'showSetupForm'])
        ->name('password.setup.show');

    Route::post('/account/setup', [PasswordSetupController::class, 'setupPassword'])
        ->middleware('throttle:auth')
        ->name('password.setup.process');
});

Route::post('/account/setup/resend', [PasswordSetupController::class, 'resendSetupEmail'])
    ->name('password.setup.resend')
    ->middleware('throttle:email');

// ============================
// DEBUG EMAIL PREVIEW ROUTES
// ============================
// IMPORTANT: Only available in debug mode - Remove before production deployment
if (config('app.debug')) {
    Route::get('/debug/email/booking-created/{bookingId}', function ($bookingId) {
        $booking = \App\Models\Booking::with([
            'user',
            'detail.tour',
            'detail.schedule',
            'detail.tourLanguage',
            'detail.meetingPoint',
            'redemption.promoCode'
        ])->findOrFail($bookingId);

        return new \App\Mail\BookingCreatedMail($booking);
    })->name('debug.email.booking-created');

    Route::get('/debug/email/password-setup/{userId}', function ($userId) {
        $user = \App\Models\User::findOrFail($userId);
        $passwordSetupService = app(\App\Services\Auth\PasswordSetupService::class);
        $token = $passwordSetupService->generateToken($user);

        return new \App\Mail\PasswordSetupMail($user, $token);
    })->name('debug.email.password-setup');


    Route::get('/test-alignet-data', function () {
        try {
            // Test 1: Config carga bien?
            $config = config('payment.gateways.alignet');

            if (!$config) {
                return response()->json(['error' => 'Config no encontrado'], 500);
            }

            // 🆕 Check if enabled from DB (like PaymentGatewayManager does)
            $settingKey = "payment.gateway.alignet";
            $settingEnabled = \App\Models\Setting::where('key', $settingKey)->value('value');

            $isEnabled = false;
            if ($settingEnabled !== null) {
                $isEnabled = filter_var($settingEnabled, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool)$settingEnabled;
            } else {
                $isEnabled = $config['enabled'] ?? false;
            }

            // Test 2: Service se puede instanciar?
            $alignetService = app(\App\Services\AlignetPaymentService::class);

            // Test 3: Preparar datos
            $testData = $alignetService->preparePaymentData(
                '123456789',
                100.00,
                [
                    'first_name' => 'Juan',
                    'last_name' => 'Perez',
                    'email' => 'modalprueba1@test.com',
                    'address' => 'Test Address',
                    'city' => 'San Jose',
                    'zip' => '10101',
                    'state' => 'SJ',
                    'country' => 'CR',
                    'description' => 'Test Tour',
                    'booking_id' => 1
                ]
            );

            return response()->json([
                'success' => true,
                'config' => [
                    'enabled_from_db' => $isEnabled,  // 🆕 Ahora muestra el valor correcto
                    'enabled_from_config' => $config['enabled'] ?? null,
                    'db_setting_value' => $settingEnabled,
                    'acquirer_id' => $config['acquirer_id'] ?? null,
                    'commerce_id' => $config['commerce_id'] ?? null,
                    'environment' => $config['environment'] ?? null,
                    'vpos2_url' => $config['urls'][$config['environment']]['vpos2'] ?? null,
                ],
                'payment_data' => $testData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error general',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    })->middleware('auth');
}
