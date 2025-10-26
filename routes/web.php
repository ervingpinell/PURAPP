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
use App\Http\Controllers\Admin\Users\RoleController;
use App\Http\Controllers\Admin\Users\UserRegisterController;
use App\Http\Controllers\Admin\MeetingPointSimpleController;
use App\Http\Controllers\Admin\TranslationController;

// Public bookings controller (split)
use App\Http\Controllers\Bookings\BookingController as PublicBookingController;

use App\Http\Middleware\SetLocale;
use App\Services\Reviews\ReviewAggregator;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

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
        // Home & Tours
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/tours', [HomeController::class, 'allTours'])->name('tours.index');
        Route::get('/tours/{tour:slug}', [HomeController::class, 'showTour'])->name('tours.show');

        // Contact
        Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
        Route::post('/contact', [HomeController::class, 'sendContact'])
            ->middleware('throttle:6,1')
            ->name('contact.send');

        // FAQ
        Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

        // Policies
        Route::get('/policies', [PoliciesController::class, 'index'])->name('policies.index');
        Route::get('/policies/{policy:slug}', [PoliciesController::class, 'show'])->name('policies.show');
        Route::get('/policies/id/{policy:policy_id}', [PoliciesController::class, 'showById'])
            ->name('policies.show.id');

        // Public reviews
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
    Route::view('/account/locked', 'auth.account-locked')->name('account.locked');
    Route::get('/auth/throttled', fn () => response()->view('errors.429'))->name('auth.throttled');

    Route::get('/unlock-account', [UnlockAccountController::class, 'form'])->name('unlock.form');
    Route::post('/unlock-account', [UnlockAccountController::class, 'send'])
        ->middleware('throttle:3,1')
        ->name('unlock.send');
    Route::get('/unlock-account/{user}/{hash}', [UnlockAccountController::class, 'process'])
        ->middleware('signed')
        ->name('unlock.process');

    Route::get('/email/verify/public/{id}/{hash}', PublicEmailVerificationController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.public');

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

        return back()->with('status', __('adminlte::auth.verify.resent_link_if_exists'));
    })->middleware('throttle:3,1')->name('verification.public.resend');

    // ============================
    // TEST EMAIL (DEV)
    // ============================
    Route::get('/send-test-email', function () {
        $booking = \App\Models\Booking::latest()->with(['user', 'detail', 'tour'])->first();
        if ($booking) {
            Mail::to($booking->user->email)->queue(new \App\Mail\BookingCreatedMail($booking));
        }
        return 'Test email queued!';
    });

    // ------------------------------
    // Profile & cart (private) — READONLY-BLOCKABLE
    // ------------------------------
    Route::middleware(['auth', 'verified', 'public.readonly'])->group(function () {
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
        Route::put('/carts/{item}',    [PublicCartController::class, 'update'])->name('public.carts.update');

        Route::post('/carts/expire',         [PublicCartController::class, 'expire'])->name('public.carts.expire');
        Route::post('/carts/refresh-expiry', [PublicCartController::class, 'refreshExpiry'])->name('public.carts.refreshExpiry');
        Route::get('/cart/count', [PublicCartController::class, 'count'])->name('cart.count.public');

        // Promo público (en sesión) desde CartController
        Route::post('/apply-promo',   [PublicCartController::class, 'applyPromo'])->name('public.carts.applyPromo');
        Route::delete('/remove-promo',[PublicCartController::class, 'removePromo'])->name('public.carts.removePromo');

        // Checkout desde carrito (público)
        Route::post('/bookings/from-cart', [PublicBookingController::class, 'storeFromCart'])->name('public.bookings.storeFromCart');
    });

    // ------------------------------
    // Admin
    // ------------------------------
    Route::middleware(['auth', 'verified', 'can:access-admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Admin profile
            Route::get('/profile', [ProfileController::class, 'adminShow'])->name('profile.show');
            Route::get('/profile/edit', [ProfileController::class, 'adminEdit'])->name('profile.edit');
            Route::post('/profile/edit', [ProfileController::class, 'adminUpdate'])->name('profile.update');

            // 2FA required inside panel
            Route::middleware('2fa.admin')->group(function () {

                // Dashboard
                Route::get('/', [DashBoardController::class, 'dashboard'])->name('home');

                // Users & Roles
                Route::resource('users', UserRegisterController::class)->except(['show']);
                Route::patch('users/{user}/lock', [UserRegisterController::class, 'lock'])->name('users.lock');
                Route::patch('users/{user}/unlock', [UserRegisterController::class, 'unlock'])->name('users.unlock');
                Route::patch('users/{user}/mark-verified', [UserRegisterController::class, 'markVerified'])->name('users.markVerified');

                Route::resource('roles', RoleController::class)->except(['show', 'create']);
                Route::patch('roles/{role}/toggle', [RoleController::class, 'toggle'])->name('roles.toggle');

                // Translations
                Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
                Route::get('translations/{type}/choose-locale', [TranslationController::class, 'chooseLocale'])->name('translations.choose-locale');
                Route::get('translations/{type}/select', [TranslationController::class, 'select'])->name('translations.select');
                Route::get('translations/{type}/{id}/edit', [TranslationController::class, 'edit'])->name('translations.edit');
                Route::post('translations/{type}/{id}/update', [TranslationController::class, 'update'])->name('translations.update');
                Route::post('translations/change-editing-locale', [TranslationController::class, 'changeEditingLocale'])->name('translations.change-editing-locale');

                // FAQs
                Route::resource('faqs', AdminFaqController::class)->except(['show']);
                Route::patch('faqs/{faq}/toggle-status', [AdminFaqController::class, 'toggleStatus'])->name('faqs.toggleStatus');

                // Tours: Cutoff
                Route::prefix('tours')->name('tours.')->group(function () {
                    Route::prefix('cutoff')->name('cutoff.')->group(function () {
                        Route::get('/', [CutOffController::class, 'edit'])->name('edit');
                        Route::put('/', [CutOffController::class, 'update'])->name('update');
                        Route::put('/tour', [CutOffController::class, 'updateTourOverrides'])->name('tour.update');
                        Route::put('/schedule', [CutOffController::class, 'updateScheduleOverrides'])->name('schedule.update');
                    });
                });

                // Tours: Images
                Route::get('tours/images', [TourImageController::class, 'pick'])->name('tours.images.pick');
                Route::prefix('tours/{tour}/images')->name('tours.images.')->group(function () {
                    Route::get('/', [TourImageController::class, 'index'])->name('index');
                    Route::post('/', [TourImageController::class, 'store'])->name('store');
                    Route::delete('/', [TourImageController::class, 'bulkDestroy'])->name('bulk-destroy');
                    Route::delete('/all', [TourImageController::class, 'destroyAll'])->name('destroyAll');
                    Route::patch('{image}', [TourImageController::class, 'update'])->name('update');
                    Route::delete('{image}', [TourImageController::class, 'destroy'])->name('destroy');
                    Route::post('{image}/cover', [TourImageController::class, 'setCover'])->name('cover');
                    Route::post('reorder', [TourImageController::class, 'reorder'])->name('reorder');
                });

                // Meeting points
                Route::resource('meetingpoints', MeetingPointSimpleController::class)->except(['show', 'create', 'edit']);
                Route::patch('meetingpoints/{meetingpoint}/toggle', [MeetingPointSimpleController::class, 'toggle'])->name('meetingpoints.toggle');

                // Tour Types (cuidado con el parámetro)
                Route::prefix('types')->name('types.')->group(function () {
                    Route::get('images', [TourTypeCoverPickerController::class, 'pick'])->name('images.pick');
                    Route::get('images/{tourType}/edit', [TourTypeCoverPickerController::class, 'edit'])->name('images.edit');
                    Route::put('images/{tourType}', [TourTypeCoverPickerController::class, 'updateCover'])->name('images.update');
                });

                // Policies
                Route::get('policies', [PolicyController::class, 'index'])->name('policies.index');
                Route::post('policies', [PolicyController::class, 'store'])->name('policies.store');
                Route::put('policies/{policy:policy_id}', [PolicyController::class, 'update'])->name('policies.update');
                Route::post('policies/{policy:policy_id}/toggle', [PolicyController::class, 'toggle'])->name('policies.toggle');
                Route::delete('policies/{policy:policy_id}', [PolicyController::class, 'destroy'])->name('policies.destroy');

                Route::get('policies/{policy:policy_id}/sections', [PolicySectionController::class, 'index'])->name('policies.sections.index');
                Route::post('policies/{policy:policy_id}/sections', [PolicySectionController::class, 'store'])->name('policies.sections.store');
                Route::put('policies/{policy:policy_id}/sections/{section}', [PolicySectionController::class, 'update'])->name('policies.sections.update');
                Route::post('policies/{policy:policy_id}/sections/{section}/toggle', [PolicySectionController::class, 'toggle'])->name('policies.sections.toggle');
                Route::delete('policies/{policy:policy_id}/sections/{section}', [PolicySectionController::class, 'destroy'])->name('policies.sections.destroy');

                // Promo Codes (admin)
                Route::get('promo-codes', [PromoCodeController::class, 'index'])->name('promoCodes.index');
                Route::post('promo-codes', [PromoCodeController::class, 'store'])->name('promoCodes.store');
                Route::delete('promo-codes/{promo}', [PromoCodeController::class, 'destroy'])->name('promoCodes.destroy');
                Route::patch('promo-codes/{promo}/operation', [PromoCodeController::class, 'updateOperation'])->name('promoCodes.updateOperation');

                // Tours (CRUD + archive/restore/purge)
                Route::resource('tours', TourController::class)->except(['create', 'edit', 'show', 'destroy']);
                Route::patch('tours/{tour:tour_id}/toggle', [TourController::class, 'toggle'])->name('tours.toggle');
                Route::delete('tours/{tour}', [TourController::class, 'destroy'])->name('tours.destroy');
                Route::post('tours/{tour}/restore', [TourController::class, 'restore'])->name('tours.restore');
                Route::delete('tours/{tour}/purge', [TourController::class, 'purge'])->name('tours.purge');
                Route::get('tour-order', [TourOrderController::class, 'index'])->name('tours.order.index');
                Route::post('tour-order/{tourType}/save', [TourOrderController::class, 'save'])->name('tours.order.save');

                // Tour submodules
                Route::prefix('tours')->name('tours.')->group(function () {
                    // Schedule
                    Route::resource('schedule', TourScheduleController::class)->except(['create','edit','show']);
                    Route::put('schedule/{schedule}/toggle', [TourScheduleController::class, 'toggle'])->name('schedule.toggle');
                    Route::post('schedule/{tour}/attach', [TourScheduleController::class, 'attach'])->name('schedule.attach');
                    Route::delete('schedule/{tour}/{schedule}/detach', [TourScheduleController::class, 'detach'])->name('schedule.detach');
                    Route::patch('schedule/{tour}/{schedule}/assignment-toggle', [TourScheduleController::class, 'toggleAssignment'])->name('schedule.assignment.toggle');

                    // Itinerary
                    Route::resource('itinerary', ItineraryController::class)->except(['show']);
                    Route::patch('itineraries/{itinerary}/toggle', [ItineraryController::class, 'toggle'])->name('itinerary.toggle');
                    Route::post('itinerary/{itinerary}/assign-items', [ItineraryController::class, 'assignItems'])->name('itinerary.assignItems');

                    // Itinerary Items
                    Route::resource('itinerary_items', ItineraryItemController::class)->except(['show', 'create', 'edit']);
                    Route::patch('itinerary_items/{itinerary_item}/toggle', [ItineraryItemController::class, 'toggle'])->name('itinerary_items.toggle');

                    // Availability
                    Route::resource('availability', TourAvailabilityController::class)->except(['show']);

                    // Excluded Dates
                    Route::get('excluded_dates', [TourExcludedDateController::class, 'index'])->name('excluded_dates.index');
                    Route::resource('excluded_dates', TourExcludedDateController::class)->except(['show','index']);
                    Route::post('excluded_dates/toggle', [TourExcludedDateController::class, 'toggle'])->name('excluded_dates.toggle');
                    Route::post('excluded_dates/bulk-toggle', [TourExcludedDateController::class, 'bulkToggle'])->name('excluded_dates.bulkToggle');
                    Route::post('excluded_dates/block-all', [TourExcludedDateController::class, 'blockAll'])->name('excluded_dates.blockAll');
                    Route::get('excluded_dates/blocked', [TourExcludedDateController::class, 'blocked'])->name('excluded_dates.blocked');

                    // Amenities
                    Route::resource('amenities', AmenityController::class)->except(['show']);
                    Route::patch('amenities/{amenity}/toggle', [AmenityController::class, 'toggle'])->name('amenities.toggle');
                });

                // ============================
                // BOOKINGS (ADMIN)
                // ============================
                // Export
                Route::get('bookings/export/excel', [AdminBookingController::class, 'exportExcel'])->name('bookings.export.excel');
                Route::get('bookings/export/pdf',   [AdminBookingController::class, 'exportPdf'])->name('bookings.export.pdf');

                // Calendar (restaurado)
                Route::get('bookings/reserved', [AdminBookingController::class, 'reservedSeats'])->name('bookings.reserved');
                Route::get('bookings/calendar-data', [AdminBookingController::class, 'calendarData'])->name('bookings.calendarData');
                Route::get('bookings/calendar', [AdminBookingController::class, 'calendar'])->name('bookings.calendar');

                // API promo verification (admin)
                Route::get('bookings/verify-promo-code', [AdminBookingController::class, 'verifyPromoCode'])->name('bookings.verifyPromoCode');

                // Receipt + status
                Route::get('bookings/{booking}/receipt', [AdminBookingController::class, 'generateReceipt'])->name('bookings.receipt');
                Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');

                // CRUD principal
                Route::resource('bookings', AdminBookingController::class)->except(['show']);
                Route::post('bookings/from-cart', [AdminBookingController::class, 'storeFromCart'])
                    ->name('bookings.storeFromCart');

                // Idiomas, Hoteles
                Route::resource('languages', TourLanguageController::class, ['parameters' => ['languages' => 'language']])->except(['show']);
                Route::patch('languages/{language}/toggle', [TourLanguageController::class, 'toggle'])->name('languages.toggle');

                Route::resource('hotels', HotelListController::class)->except(['show', 'create', 'edit']);
                Route::post('hotels/sort', [HotelListController::class, 'sort'])->name('hotels.sort');
                Route::patch('hotels/{hotel}/toggle', [HotelListController::class, 'toggle'])->name('hotels.toggle');

                // Tipos de tour
                Route::resource('tourtypes', TourTypeController::class, ['parameters' => ['tourtypes' => 'tourType']])->except(['show']);
                Route::put('tourtypes/{tourType}/toggle', [TourTypeController::class, 'toggle'])->name('tourtypes.toggle');

                // ============================
                // CART (ADMIN)
                // ============================
                Route::get('carts', [AdminCartController::class, 'index'])->name('carts.index');
                Route::post('carts', [AdminCartController::class, 'store'])->name('carts.store');
                Route::patch('carts/{item}', [AdminCartController::class, 'update'])->name('carts.update');
                Route::delete('carts/item/{item}', [AdminCartController::class, 'destroy'])->name('carts.item.destroy');

                Route::get('carts/all', [AdminCartController::class, 'allCarts'])->name('carts.all');
                Route::delete('carts/{cart}', [AdminCartController::class, 'destroyCart'])->name('carts.destroy');
                Route::patch('carts/{cart}/toggle', [AdminCartController::class, 'toggleActive'])->name('carts.toggle');

                Route::post('carts/apply-promo', [AdminCartController::class, 'applyPromoAdmin'])->name('carts.applyPromo');
                Route::delete('carts/remove-promo', [AdminCartController::class, 'removePromoAdmin'])->name('carts.removePromo');

                // Reports
                Route::prefix('reports')->name('reports.')->group(function () {
                    Route::get('/', [ReportsController::class, 'index'])->name('index');
                    Route::get('/chart/monthly-sales', [ReportsController::class, 'chartMonthlySales'])->name('chart.monthly');
                    Route::get('/chart/by-language',   [ReportsController::class, 'chartByLanguage'])->name('chart.language');
                });

                // Reviews management (permission manage-reviews)
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
                    Route::get('reviews', [ReviewAdminController::class, 'index'])->name('reviews.index');
                    Route::get('reviews/create', [ReviewAdminController::class, 'create'])->name('reviews.create');
                    Route::post('reviews', [ReviewAdminController::class, 'store'])->name('reviews.store');
                    Route::get('reviews/{review}/edit', [ReviewAdminController::class, 'edit'])->name('reviews.edit');
                    Route::put('reviews/{review}', [ReviewAdminController::class, 'update'])->name('reviews.update');
                    Route::delete('reviews/{review}', [ReviewAdminController::class, 'destroy'])->name('reviews.destroy');

                    Route::post('reviews/{review}/publish', [ReviewAdminController::class, 'publish'])->name('reviews.publish');
                    Route::post('reviews/{review}/hide', [ReviewAdminController::class, 'hide'])->name('reviews.hide');
                    Route::post('reviews/{review}/flag', [ReviewAdminController::class, 'flag'])->name('reviews.flag');
                    Route::post('reviews/bulk', [ReviewAdminController::class, 'bulk'])->name('reviews.bulk');

                    // Replies & Threads
                    Route::get('reviews/{review}/replies/create', [ReviewReplyController::class, 'create'])->name('reviews.replies.create');
                    Route::post('reviews/{review}/replies', [ReviewReplyController::class, 'store'])->name('reviews.replies.store');
                    Route::delete('reviews/{review}/replies/{reply}', [ReviewReplyController::class, 'destroy'])->name('reviews.replies.destroy');
                    Route::post('reviews/{review}/replies/{reply}/toggle', [ReviewReplyController::class, 'toggle'])->name('reviews.replies.toggle');
                    Route::get('reviews/{review}/thread', [ReviewReplyController::class, 'thread'])->name('reviews.replies.thread');

                    // Review Requests
                    Route::get('review-requests', [ReviewRequestAdminController::class, 'index'])->name('review-requests.index');
                    Route::post('review-requests/{booking}/send', [ReviewRequestAdminController::class, 'send'])->name('review-requests.send');
                    Route::post('review-requests/{rr}/resend', [ReviewRequestAdminController::class, 'resend'])->name('review-requests.resend');
                    Route::delete('review-requests/{rr}', [ReviewRequestAdminController::class, 'destroy'])->name('review-requests.destroy');
                });
            });
        });

    // ============================
    // COOKIES (consent)
    // ============================
    Route::post('/cookies/accept', [CookieConsentController::class, 'accept'])
        ->name('cookies.accept')
        ->middleware('throttle:10,1');

    Route::post('/cookies/reject', [CookieConsentController::class, 'reject'])
        ->name('cookies.reject')
        ->middleware('throttle:10,1');

});
