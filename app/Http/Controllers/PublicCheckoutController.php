<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Policy;
use App\Models\PolicySection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log, Mail};
use Illuminate\Support\Str;
use App\Services\Policies\PolicySnapshotService;

/**
 * PublicCheckoutController
 *
 * Handles guest checkout process.
 */
class PublicCheckoutController extends Controller
{
    /** ===================== Helpers ===================== */

    private function findActiveCartForUser(?int $userId): ?Cart
    {
        if (!$userId) return null;

        return Cart::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->with([
                'items.tour',
                'items.schedule',
                'items.language',
                'items.hotel',
                'items.meetingPoint',
            ])
            ->latest('updated_at')
            ->first();
    }

    /**
     * Devuelve el primer registro de traducción por locale con fallback.
     * Acepta colecciones Eloquent de traducciones que tengan campo 'locale'.
     */
    private function pickTranslation($translations, string $locale, string $fallback)
    {
        $norm = fn($v) => str_replace('-', '_', strtolower((string)$v));

        $bag = collect($translations ?? []);
        $locNorm = $norm($locale);
        $fbNorm  = $norm($fallback);

        // exacta
        if ($t = $bag->first(fn($x) => $norm($x->locale) === $locNorm)) return $t;

        // variantes comunes (en, pt_BR, etc.)
        $alts = [$locale, str_replace('_', '-', $locale), substr($locale, 0, 2), 'pt_BR', 'pt-br'];
        foreach ($alts as $alt) {
            if ($t = $bag->first(fn($x) => $norm($x->locale) === $norm($alt))) return $t;
        }

        // fallback y fallback corto
        if ($t = $bag->first(fn($x) => $norm($x->locale) === $fbNorm)) return $t;
        if ($t = $bag->first(fn($x) => $norm($x->locale) === $norm(substr($fallback, 0, 2)))) return $t;

        // primero disponible
        return $bag->first();
    }

    /**
     * Convierte un slug a una "key" canónica para el bloque (terms, privacy, etc.).
     */
    private function canonicalKeyFromSlug(?string $slug): ?string
    {
        if (!$slug) return null;

        $s = Str::of($slug)
            ->lower()
            ->replace(' ', '-')
            ->replace('_', '-')
            ->toString();

        $map = [
            'terms'        => ['terms', 'terms-and-conditions', 't-and-c', 'tyc'],
            'privacy'      => ['privacy', 'privacy-policy'],
            'cancellation' => ['cancellation', 'cancellation-policy'],
            'refunds'      => ['refunds', 'refund', 'refund-policy', 'refunds-and-warranty', 'refunds-warranty'],
            'warranty'     => ['warranty', 'guarantee', 'warranty-policy'],
            'payments'     => ['payments', 'payment-methods', 'payment-policy'],
        ];

        foreach ($map as $key => $alts) {
            foreach ($alts as $alt) {
                if ($s === $alt || Str::contains($s, $alt)) {
                    return $key;
                }
            }
        }

        return Str::slug($s);
    }


    /**
     * Construye bloques de políticas directamente desde BD.
     * Retorna [ 'blocks' => array, 'versions' => ['terms'=>?, 'privacy'=>?] ]
     */
    /**
     * Construye bloques de políticas directamente desde BD.
     * Retorna [ 'blocks' => array, 'versions' => ['terms'=>?, 'privacy'=>?] ]
     */
    private function buildPolicyBlocksFromDB(string $locale, string $fallback): array
    {
        // Traer políticas activas con sus secciones activas + traducciones
        $policies = Policy::query()
            ->with([
                'translations',
                'sections' => function ($q) {
                    $q->orderBy('sort_order')
                        ->orderBy('section_id');
                },
                'sections.translations',
            ])
            ->where('is_active', true)
            ->orderBy('policy_id')
            ->get();

        $blocks   = [];
        $versions = ['terms' => null, 'privacy' => null];

        foreach ($policies as $p) {
            $pTr = $this->pickTranslation($p->translations, $locale, $fallback);

            $htmlParts = [];

            // =========================
            // 1) Contenido a nivel de Policy (policies_translations.content)
            // =========================
            $policyTitle   = trim((string) ($pTr->name ?? ''));
            $policyContent = (string) ($pTr->content ?? '');

            if (trim(strip_tags($policyContent)) !== '') {
                // NO metemos el título aquí porque ya lo muestra el header del bloque
                $htmlParts[] = $policyContent;
            }

            // =========================
            // 2) Contenido por secciones activas (policy_sections + translations)
            // =========================
            foreach ($p->sections ?? [] as $sec) {
                if (!$sec->is_active) {
                    continue;
                }

                $sTr     = $this->pickTranslation($sec->translations, $locale, $fallback);
                $sTitle  = trim((string) ($sTr->name ?? ''));
                $sBody   = (string) ($sTr->content ?? '');

                if ($sTitle !== '') {
                    $htmlParts[] = '<h4>' . e($sTitle) . '</h4>';
                }
                if (trim(strip_tags($sBody)) !== '') {
                    $htmlParts[] = $sBody; // ya viene HTML administrado
                }
            }

            // Si no hubo contenido NI en policy.content NI en sus secciones, la saltamos
            $hasContent = collect($htmlParts)->contains(function ($chunk) {
                return trim(strip_tags((string) $chunk)) !== '';
            });

            if (!$hasContent) {
                continue;
            }

            // =========================
            // 3) Título y versión del bloque
            // =========================
            $blockTitle = $policyTitle;
            if ($blockTitle === '') {
                $blockTitle = (string) ($p->slug ?? '');
            }
            if ($blockTitle === '') {
                $blockTitle = 'Policy #' . $p->policy_id;
            }

            // Versionado legible si la policy tiene rango de vigencia
            $version = null;
            if (!empty($p->effective_from) || !empty($p->effective_to)) {
                $from = $p->effective_from ? Carbon::parse($p->effective_from)->format('Y-m-d') : '—';
                $to   = $p->effective_to   ? Carbon::parse($p->effective_to)->format('Y-m-d') : '—';
                $version = "v {$from} → {$to}";
            }

            // =========================
            // 4) Key canónica basada en el slug (siempre en inglés)
            // =========================
            $key = $this->canonicalKeyFromSlug($p->slug);
            if (!$key) {
                $key = 'policy_' . $p->policy_id;
            }

            $blocks[] = [
                'key'     => $key,
                'title'   => $blockTitle,
                'version' => $version ?: 'v1',
                'html'    => implode("\n", $htmlParts),
            ];

            // Guardamos versiones oficiales para terms/privacy si aplica
            if ($key === 'terms'   && !$versions['terms']) {
                $versions['terms'] = $version ?: 'v1';
            }
            if ($key === 'privacy' && !$versions['privacy']) {
                $versions['privacy'] = $version ?: 'v1';
            }
        }

        // =========================
        // 5) Orden lógico de los bloques en el checkout
        // =========================
        $preferredOrder = [
            'terms',
            'privacy',
            'cancellation',
            'refunds',
            'warranty',
            'payments',
        ];
        $orderIndex = array_flip($preferredOrder);

        usort($blocks, function ($a, $b) use ($orderIndex) {
            $ka = $a['key'] ?? '';
            $kb = $b['key'] ?? '';

            $ia = $orderIndex[$ka] ?? PHP_INT_MAX;
            $ib = $orderIndex[$kb] ?? PHP_INT_MAX;

            if ($ia === $ib) {
                return strcmp($ka, $kb);
            }

            return $ia <=> $ib;
        });

        return [
            'blocks'   => $blocks,
            'versions' => $versions,
        ];
    }


    /**
     * Calcula cutoff de cancelación gratuita usando el setting booking.allow_cancellation
     */
    private function computeFreeCancelUntil(Cart $cart): ?Carbon
    {
        $tz = config('app.timezone', 'America/Costa_Rica');
        $cancellationHours = (int) setting('booking.cancellation_hours', 24); // Default 24h si no existe

        $starts = $cart->items->map(function ($it) use ($tz) {
            $date = $it->tour_date ?? null;
            $time = optional($it->schedule)->start_time;
            if (!$date || !$time) return null;
            return Carbon::parse("{$date} {$time}", $tz);
        })->filter();

        return $starts->isNotEmpty() ? $starts->min()->copy()->subHours($cancellationHours) : null;
    }

    /** ===================== Acciones ===================== */

    public function show(Request $request, PolicySnapshotService $svc)
    {
        $userId = Auth::id();
        Log::info('Checkout Show - Start', ['userId' => $userId, 'session_id' => session()->getId()]);

        // Support both authenticated users and guests
        if ($userId) {
            // Authenticated user - use DB cart
            $cart = $this->findActiveCartForUser($userId);
            $itemsCount = $cart ? $cart->items()->count() : 0;

            if (!$cart || $itemsCount === 0) {
                // FALLBACK: Check if there are guest items in session (e.g. added before login or during weird state)
                $sessionCartItems = session('guest_cart_items', []);
                if (!empty($sessionCartItems)) {
                    Log::info('Checkout Show - Auth User with Empty DB Cart but Session Items found. Using Mock Cart.');
                    // Fall through to guest logic (treat as guest for checkout view)
                    $userId = null; // Force null to trigger guest logic below
                } else {
                    Log::info('Checkout Show - Redirecting Auth User (Empty Cart)');
                    return redirect()->route(app()->getLocale() . '.home')
                        ->with('cart_expired', true);
                }
            }
        }

        // Re-check userId because we might have forced it to null above
        if ($userId) {
            // Logic for REAL DB CART (already loaded above)
            // We need to keep this block separate if we want to avoid code duplication or nested elses
            // But simpler: if $userId is still set, we use $cart from DB.
        } else {
            // Guest user (OR Fallback Auth) - check session cart
            $sessionCartItems = session('guest_cart_items', []);
            Log::info('Checkout Show - Guest Cart Items', ['count' => count($sessionCartItems), 'items' => $sessionCartItems]);


            if (empty($sessionCartItems)) {
                Log::info('Checkout Show - Redirecting Guest (Empty Session Cart)');
                return redirect()->route(app()->getLocale() . '.home')
                    ->with('cart_expired', true);
            }

            // Create a mock cart object for guest to display in checkout
            $cart = (object) [
                'items' => collect($sessionCartItems)->map(function ($item) {
                    return (object) array_merge($item, [
                        'tour' => \App\Models\Tour::find($item['tour_id']),
                        'schedule' => \App\Models\Schedule::find($item['schedule_id']),
                        'language' => \App\Models\TourLanguage::find($item['tour_language_id']),
                        'hotel' => isset($item['hotel_id']) ? \App\Models\HotelList::find($item['hotel_id']) : null,
                        'meetingPoint' => isset($item['meeting_point_id']) ? \App\Models\MeetingPoint::find($item['meeting_point_id']) : null,
                    ]);
                }),
                'is_guest_cart' => true,
            ];

            $itemsCount = $cart->items->count();
        }

        $locale   = app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'es');

        // 1) Siempre intentamos armar los bloques desde BD
        $dbPack   = $this->buildPolicyBlocksFromDB($locale, $fallback);
        $blocks   = $dbPack['blocks'] ?? [];
        $versions = $dbPack['versions'] ?? ['terms' => null, 'privacy' => null];

        // 2) Snapshot de config SOLO para versiones / compat (no para mostrar contenido)
        $cfgPack = $svc->make();

        // Calcular cutoff de cancelación gratuita (24h antes del primer tour)
        if (isset($cart->items) && method_exists($cart, 'items')) {
            $freeCancelUntil = $this->computeFreeCancelUntil($cart);
        } else {
            $freeCancelUntil = null; // Guest cart doesn't have expiry method
        }

        // Versiones visibles (si BD no trae nada, cae a config o v1)
        $termsVersion = $versions['terms']
            ?? ($cfgPack['versions']['terms']   ?? 'v1');

        $privacyVersion = $versions['privacy']
            ?? ($cfgPack['versions']['privacy'] ?? 'v1');

        return view('public.checkout', [
            'cart'            => $cart,

            // === El partial ya no tiene fallback hardcode: siempre mostramos lo que venga de BD ===
            'policyBlocks'    => $blocks,

            'termsVersion'    => $termsVersion,
            'privacyVersion'  => $privacyVersion,
            'freeCancelUntil' => $freeCancelUntil,

            // Se mantiene por compatibilidad con otros usos (no lo usa el include nuevo)
            'policies'        => $cfgPack['snapshot'] ?? [],
        ]);
    }


    public function process(Request $request, PolicySnapshotService $svc)
    {
        $userId = Auth::id();

        // Guest checkout support
        if (!$userId) {
            // Check if guest data is provided
            if ($request->has('guest_name') && $request->has('guest_email')) {
                return $this->processGuestCheckout($request, $svc);
            }

            // No auth and no guest data - redirect to login
            return redirect()->route('login');
        }

        // Check if this is a booking payment (from admin-created booking)
        $bookingId = session('pending_booking_payment');
        if ($bookingId) {
            // Validate terms acceptance
            $request->validate([
                'accept_terms' => ['required', 'accepted'],
                'scroll_ok'    => ['required', 'in:1'],
            ], [
                'accept_terms.required' => __('Debes aceptar los Términos y Políticas para continuar'),
                'accept_terms.accepted' => __('Debes aceptar los Términos y Políticas para continuar'),
            ]);

            // Record terms acceptance for booking payment
            $booking = \App\Models\Booking::find($bookingId);
            if ($booking) {
                $locale = app()->getLocale();
                $policySnapshot = $svc->make($locale);

                try {
                    DB::table('terms_acceptances')->updateOrInsert(
                        ['booking_ref' => $booking->booking_id],
                        [
                            'user_id'           => $userId,
                            'cart_ref'          => null,
                            'accepted_at'       => now(),
                            'terms_version'     => $policySnapshot['versions']['terms'] ?? 'v1',
                            'privacy_version'   => $policySnapshot['versions']['privacy'] ?? 'v1',
                            'policies_snapshot' => json_encode($policySnapshot['snapshot'] ?? [], JSON_UNESCAPED_UNICODE),
                            'policies_sha256'   => $policySnapshot['sha256'] ?? '',
                            'ip_address'        => $request->ip(),
                            'user_agent'        => (string) $request->userAgent(),
                            'locale'            => $locale,
                            'timezone'          => config('app.timezone'),
                            'consent_source'    => 'checkout_booking_payment',
                            'referrer'          => $request->headers->get('referer'),
                            'updated_at'        => now(),
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to record terms acceptance for booking payment', [
                        'error' => $e->getMessage(),
                        'booking_id' => $bookingId,
                    ]);
                }
            }

            // Clear session and redirect to payment
            session()->forget('pending_booking_payment');
            return redirect()->route('payment.show', ['booking_id' => $bookingId]);
        }

        // Normal cart checkout flow
        $cart = $this->findActiveCartForUser($userId);
        if (!$cart || $cart->items()->count() === 0) {
            // Check for session cart fallback (Hybrid state)
            $sessionCartItems = session('guest_cart_items', []);

            if (!empty($sessionCartItems) && $userId) {
                Log::info('Checkout Process - Fallback: Migrating session items to Auth DB Cart');

                if (!$cart) {
                    try {
                        $cart = \App\Models\Cart::create([
                            'user_id' => $userId,
                            'is_active' => true,
                            'expires_at' => now()->addMinutes((int) setting('cart.expiration_minutes', 30))
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Check for Foreign Key violation (User ID not found)
                        if ($e->getCode() == '23000' || $e->getCode() == '23503') {
                            Log::error('Checkout Process - Ghost User Detected (Auth ID valid but DB User missing). Forcing Logout.', ['id' => $userId]);
                            Auth::logout();
                            return redirect()->route('login')
                                ->with('error', __('Tu sesión no es válida. Por favor inicia sesión nuevamente.'));
                        }
                        throw $e;
                    }
                }

                // Persist items using our helper
                $this->persistSessionItemsToCart($cart, $sessionCartItems);

                // Reload items relationships for next steps
                $cart->load(['items.tour.prices.category', 'items.schedule', 'items.language', 'items.hotel', 'items.meetingPoint']);

                // Do NOT return redirect. Allow flow to continue with $cart.
            } else {
                return redirect()->route(app()->getLocale() . '.home')
                    ->with('cart_expired', true);
            }
        }

        // Terms are now accepted on the payment page, so we skip validation and recording here.

        // 🔄 NUEVO FLUJO: Guardar snapshot del carrito en sesión (NO crear bookings aún)
        return $this->processCartSnapshot($request, $cart);
    }

    /**
     * Process guest checkout
     */
    private function processGuestCheckout(Request $request, PolicySnapshotService $svc)
    {
        // Validate guest data
        $request->validate([
            'guest_first_name' => ['required', 'string', 'max:100'],
            'guest_last_name' => ['required', 'string', 'max:100'],
            // 'guest_name' => ['required', 'string', 'max:255'], // Legacy field, might be present but we rely on split inputs
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:50'],
            'guest_address' => ['required', 'string', 'max:255'],
            'guest_city' => ['required', 'string', 'max:100'],
            'guest_state' => ['required', 'string', 'max:100'],
            'guest_zip' => ['required', 'string', 'max:20'],
            'guest_country' => ['required', 'string', 'size:2'], // ISO 2 chars
        ]);


        // NO user creation here - will be done after payment success
        try {

            // Store guest info in session ONLY (user created after payment)
            session([
                'guest_user_email' => $request->input('guest_email'),
                'guest_user_name' => $request->input('guest_first_name') . ' ' . $request->input('guest_last_name'),
                'guest_user_first_name' => $request->input('guest_first_name'),
                'guest_user_last_name' => $request->input('guest_last_name'),
                'guest_user_phone' => $request->input('guest_phone'),
                'guest_user_address' => $request->input('guest_address'),
                'guest_user_city' => $request->input('guest_city'),
                'guest_user_state' => $request->input('guest_state'),
                'guest_user_zip' => $request->input('guest_zip'),
                'guest_user_country' => $request->input('guest_country'),
                'is_guest_session' => true,
            ]);

            // Get guest cart from session
            $sessionCartItems = session('guest_cart_items', []);

            if (empty($sessionCartItems)) {
                return redirect()->route(app()->getLocale() . '.home')
                    ->with('cart_expired', true);
            }

            // Create an ANONYMOUS Cart (User will be created/linked after payment success)
            // This prevents creating "junk" users if payment fails or is abandoned.
            $cart = \App\Models\Cart::create([
                'user_id' => null, // Nullable now
                'guest_email' => $request->guest_email,
                'guest_name' => $request->input('guest_first_name') . ' ' . $request->input('guest_last_name'),
                'is_active' => true,
                'expires_at' => now()->addMinutes((int) setting('cart.expiration_minutes', 30))
            ]);

            // Persist session items to DB helper
            $this->persistSessionItemsToCart($cart, $sessionCartItems);

            // Do NOT clear session cart yet. If validation fails in processCartSnapshot,
            // the user is redirected back to cart. If session is empty, they lose their items.
            // We should only clear this after successful payment/booking.
            // session()->forget('guest_cart_items');

            return $this->processCartSnapshot($request, $cart);
        } catch (\Exception $e) {
            Log::error('[GuestCheckout] Failed to process guest checkout', [
                'error' => $e->getMessage(),
                'email' => $request->input('guest_email'),
            ]);

            return back()->withErrors([
                'guest_email' => __('Unable to process guest checkout. Please try again.')
            ])->withInput();
        }
    }
    /**
     * Process cart snapshot and redirect to payment
     * Bookings will be created AFTER successful payment
     */
    private function processCartSnapshot(Request $request, $cart)
    {
        // Get user from Auth or guest session
        $user = Auth::user();
        $userId = $user ? $user->user_id : null; // Guest carts don't have a user_id yet

        // If it's a guest session, retrieve guest data from session
        $guestEmail = session('guest_user_email');
        $guestName = session('guest_user_name');
        $guestFirstName = session('guest_user_first_name');
        $guestLastName = session('guest_user_last_name');

        if (!$userId && !$guestEmail) {
            return redirect()->route('login')
                ->with('error', __('Please login or provide guest details to continue'));
        }

        $notes = trim((string) ($request->input('notes') ?? session('checkout_notes', '')));

        // Get promo code if exists
        $promoCodeValue = $request->input('promo_code') ?? $request->session()->get('public_cart_promo.code');
        $promoCode = null;

        if ($promoCodeValue) {
            // simplified retrieval for brevity, assuming model exists
            $clean = \App\Models\PromoCode::normalize($promoCodeValue);
            $promoCode = \App\Models\PromoCode::whereRaw("TRIM(REPLACE(code, ' ', '')) = ?", [$clean])->first();
        }
        $promoSession = $request->session()->get('public_cart_promo');

        // Calculate totals
        $subtotal = $cart->calculateTotal();
        $discountAmount = 0.0;

        if ($promoCode && isset($promoSession['discount_amount'])) {
            $discountAmount = (float) $promoSession['discount_amount'];
        }

        $total = max(0, $subtotal - $discountAmount);

        // Create cart snapshot for session
        $cartSnapshot = [
            'user_id' => $userId, // Will be null for guest carts
            'cart_id' => $cart->cart_id,
            'guest_email' => $guestEmail, // Store guest email in snapshot
            'guest_name' => $guestName,   // Store guest name in snapshot
            'guest_first_name' => $guestFirstName,
            'guest_last_name' => $guestLastName,
            'notes' => $notes !== '' ? $notes : null,
            'promo_code' => $promoCode?->code,
            'promo_code_id' => $promoCode?->promo_code_id,
            'promo_snapshot' => ($promoCode && isset($promoSession['code']) && $promoSession['code'] === $promoCode->code) ? $promoSession : null,
            'items' => $cart->items->map(function ($item) {
                return [
                    'cart_item_id' => $item->cart_item_id,
                    'tour_id' => $item->tour_id,
                    'schedule_id' => $item->schedule_id,
                    'tour_language_id' => $item->tour_language_id,
                    'tour_date' => $item->tour_date,
                    'categories' => $item->categories,
                    'hotel_id' => $item->is_other_hotel ? null : $item->hotel_id,
                    'is_other_hotel' => (bool) $item->is_other_hotel,
                    'other_hotel_name' => $item->is_other_hotel ? $item->other_hotel_name : null,
                    'meeting_point_id' => $item->meeting_point_id,
                ];
            })->toArray(),
            'subtotal' => $subtotal,
            'total' => $total,
            'currency' => config('payment.default_currency', 'USD'),
            'created_at' => now()->toIso8601String(),
        ];

        Log::info('Checkout Process - Snapshot Created:', [
            'cart_id' => $cartSnapshot['cart_id'],
            'item_count' => count($cartSnapshot['items']),
            'promo_code' => $cartSnapshot['promo_code'],
            'subtotal' => $cartSnapshot['subtotal'],
            'keys' => array_keys($cartSnapshot)
        ]);

        // Store snapshot in session
        $request->session()->put('cart_snapshot', $cartSnapshot);
        $request->session()->save();

        // Mark cart items as reserved (for capacity hold)
        $reservationToken = \Illuminate\Support\Str::random(32);
        $cart->items()->update([
            'is_reserved' => true,
            'reserved_at' => now(),
            'reservation_token' => $reservationToken,
        ]);

        $request->session()->put('cart_reservation_token', $reservationToken);
        // $request->session()->forget('public_cart_promo'); // Keep promo code in session for back navigation
        $request->session()->forget('checkout_notes');

        // Redirect to payment page (bookings will be created after payment success)
        return redirect()->route('payment.show')
            ->with('info', __('Please complete payment to confirm your booking'));
    }

    /**
     * Show checkout via token (for admin-created bookings)
     * NO automatic login - user can complete payment without authentication
     */
    public function showByToken(string $token, PolicySnapshotService $svc)
    {
        $booking = \App\Models\Booking::where('checkout_token', $token)
            ->with(['detail', 'tour', 'user', 'detail.schedule', 'detail.tourLanguage'])
            ->firstOrFail();

        if (!$booking->isCheckoutTokenValid()) {
            abort(403, __('m_bookings.checkout_link_expired'));
        }

        // Check if booking is already paid
        if ($booking->isPaid()) {
            abort(403, __('m_bookings.booking_already_paid'));
        }

        // Mark checkout as accessed (start timer)
        if (!$booking->checkout_accessed_at) {
            $booking->checkout_accessed_at = now();
            $booking->save();
        }

        // NO automatic login - allow guest-like checkout
        // Store booking info in session for payment processing
        session([
            'pending_booking_payment' => $booking->booking_id,
            'booking_payment_token' => $token,
            'booking_user_id' => $booking->user_id,
        ]);

        // Get policy blocks for checkout page
        $locale   = app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'es');

        $result = $this->buildPolicyBlocksFromDB($locale, $fallback);
        $blocks = $result['blocks'];
        $versions = $result['versions'];

        $policySnapshot = $svc->make($locale);
        $termsVersion = $versions['terms']
            ?? ($policySnapshot['versions']['terms'] ?? 'v1');
        $privacyVersion = $versions['privacy']
            ?? ($policySnapshot['versions']['privacy'] ?? 'v1');

        // Calculate free cancel until
        $tz = config('app.timezone', 'America/Costa_Rica');
        $cancellationHours = (int) setting('booking.cancellation_hours', 24);

        $tourDate = $booking->tour_date;
        $startTime = optional($booking->detail?->schedule)->start_time;

        $freeCancelUntil = null;
        if ($tourDate && $startTime) {
            $freeCancelUntil = Carbon::parse("{$tourDate} {$startTime}", $tz)
                ->subHours($cancellationHours);
        }

        // Create a mock cart structure for the view
        $mockCart = (object) [
            'cart_id' => null,
            'user_id' => $booking->user_id,
            'is_active' => true,
            'expires_at' => now()->addMinutes((int) setting('cart.expiration_minutes', 30)),
            'is_booking_payment' => true, // Flag for view
            'items' => collect([
                (object) [
                    'cart_item_id' => null,
                    'tour_id' => $booking->tour_id,
                    'tour' => $booking->tour,
                    'tour_date' => $booking->tour_date,
                    'schedule' => $booking->detail?->schedule,
                    'language' => $booking->detail?->tourLanguage,
                    'categories' => $booking->detail?->categories ?? [],
                    'hotel' => $booking->detail?->hotel_id ? \App\Models\HotelList::find($booking->detail->hotel_id) : null,
                    'meetingPoint' => $booking->detail?->meeting_point_id ? \App\Models\MeetingPoint::find($booking->detail->meeting_point_id) : null,
                    'notes' => $booking->notes,
                    'special_requests' => $booking->notes,
                ]
            ])
        ];

        return view('public.checkout', [
            'cart'            => $mockCart,
            'policyBlocks'    => $blocks,
            'termsVersion'    => $termsVersion,
            'privacyVersion'  => $privacyVersion,
            'freeCancelUntil' => $freeCancelUntil,
            'policies'        => $policySnapshot['snapshot'] ?? [],
            'isBookingPayment' => true, // Flag to indicate this is a booking payment
            'booking'         => $booking, // Pass booking for reference
            'bookingUser'     => $booking->user, // Pass user data for prefilling forms
        ]);
    }

    /**
     * Show payment page for pay-later booking using booking reference
     */
    public function showPayment(string $bookingReference)
    {
        $booking = \App\Models\Booking::where('booking_reference', $bookingReference)
            ->with(['detail', 'tour', 'user', 'detail.schedule', 'detail.tourLanguage'])
            ->firstOrFail();

        // Check if booking is already paid
        if ($booking->is_paid) {
            return redirect()->route('user.bookings')
                ->with('info', __('This booking has already been paid'));
        }

        // Check if payment link has expired
        if ($booking->payment_link_expires_at && $booking->payment_link_expires_at < now()) {
            return view('public.payment-expired', ['booking' => $booking])
                ->with('error', __('Payment link has expired. Please contact us for assistance.'));
        }

        // Check if booking itself has expired
        if ($booking->pending_expires_at && $booking->pending_expires_at < now()) {
            return view('public.booking-expired', ['booking' => $booking])
                ->with('error', __('This booking has expired'));
        }

        // Login the user if not already logged in
        if (!Auth::check() || Auth::id() !== $booking->user_id) {
            Auth::loginUsingId($booking->user_id);
        }

        // Store booking reference in session for payment processing
        session(['pending_payment_booking_ref' => $bookingReference]);

        // Redirect to payment gateway
        return redirect()->route('payment.show', ['booking_id' => $booking->booking_id])
            ->with('info', __('Please complete payment to confirm your booking'));
    }

    /**
     * Handle successful payment for pay-later booking
     */
    public function handlePaymentSuccess(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $booking = \App\Models\Booking::find($bookingId);

        if (!$booking) {
            return redirect()->route(app()->getLocale() . '.home')
                ->with('error', __('Booking not found'));
        }

        DB::beginTransaction();
        try {
            // Mark booking as paid
            $booking->is_paid = true;
            $booking->paid_amount = $booking->total;
            $booking->paid_at = now();
            $booking->pending_expires_at = null; // Clear expiration
            $booking->status = 'confirmed'; // Update status to confirmed
            $booking->save();

            // Send confirmation emails
            try {
                // Customer confirmation
                \Mail::to($booking->user->email)
                    ->send(new \App\Mail\PaymentSuccessMail($booking));

                // Admin notification
                $adminEmail = setting('email.notification_email', config('booking.email_config.from', 'admin@example.com'));
                \Mail::to($adminEmail)
                    ->send(new \App\Mail\NewPaidBookingAdmin($booking));
            } catch (\Exception $e) {
                Log::error('Failed to send payment success emails', [
                    'booking_id' => $bookingId,
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();

            Log::info('[PaymentSuccess] Booking marked as paid', [
                'booking_id' => $bookingId,
                'reference' => $booking->booking_reference,
                'amount' => $booking->paid_amount
            ]);

            // Clear promo code from session (crucial for guests)
            session()->forget('public_cart_promo');

            // Smart redirect based on user state
            $redirectUrl = $this->getPostPaymentRedirect($booking);

            return redirect($redirectUrl)
                ->with('success', __('Payment successful! Your booking is confirmed.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[PaymentSuccess] Failed to process payment', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);

            // Smart redirect on error too
            $redirectUrl = $this->getPostPaymentRedirect($booking);

            return redirect($redirectUrl)
                ->with('error', __('Failed to process payment. Please contact support.'));
        }
    }

    /**
     * Get smart redirect route after payment success
     * Based on user authentication state and password status
     */
    protected function getPostPaymentRedirect($booking): string
    {
        $user = $booking->user;

        // Case 1: User doesn't exist (safety check)
        if (!$user) {
            return route('home');
        }

        // Case 2: User has no password (guest checkout) → Password setup
        if (!$user->password) {
            try {
                // Generate password setup token
                $passwordSetupService = app(\App\Services\Auth\PasswordSetupService::class);
                $tokenData = $passwordSetupService->generateSetupToken($user);

                return route('password.setup.show', ['token' => $tokenData['plain_token']])
                    . '?from=payment';
            } catch (\Exception $e) {
                \Log::error('Failed to generate password setup token', [
                    'user_id' => $user->user_id,
                    'error' => $e->getMessage()
                ]);
                // Fallback to login
                return route('login') . '?redirect=my-bookings';
            }
        }

        // Case 3: User not authenticated → Login
        if (!\Auth::check()) {
            return route('login') . '?redirect=my-bookings';
        }

        // Case 4: User authenticated → My bookings
        return route('my-bookings');
    }

    /**
     * Helper to migrate/persist session cart items into a newly created (or existing) DB Cart.
     */
    private function persistSessionItemsToCart($cart, array $sessionCartItems)
    {
        foreach ($sessionCartItems as $itemData) {
            // Expand categories with price data
            $expandedCats = [];
            $rawCats = $itemData['categories'] ?? [];

            // Load Tour to get prices
            $tourId = $itemData['tour_id'] ?? null;
            $tourDate = $itemData['tour_date'] ?? null;

            if ($tourId && is_array($rawCats)) {
                // Check if categories are already in snapshot format (from buildCategoriesSnapshot)
                // Format: [['category_id' => 1, 'quantity' => 12, 'price' => 75.00, ...], ...]
                $firstItem = reset($rawCats);
                $isSnapshot = is_array($firstItem) && isset($firstItem['category_id'], $firstItem['quantity'], $firstItem['price']);

                if ($isSnapshot) {
                    // Categories are already in the correct format with prices!
                    // Just use them directly
                    foreach ($rawCats as $catData) {
                        if (!is_array($catData) || ($catData['quantity'] ?? 0) <= 0) continue;

                        $expandedCats[] = [
                            'category_id' => $catData['category_id'],
                            'quantity' => $catData['quantity'],
                            'price' => $catData['price'],
                            'name' => $catData['category_name'] ?? 'Category',
                        ];
                    }
                } else {
                    // Legacy format: ['category_id' => quantity, ...]
                    // This shouldn't happen with current CartController, but handle it for safety
                    $tour = \App\Models\Tour::with('prices.category')->find($tourId);

                    if ($tour) {
                        foreach ($rawCats as $catId => $qty) {
                            $qty = (int)$qty;
                            if ($qty <= 0) continue;

                            $priceVal = 0;

                            // Find valid price for date
                            $priceModel = $tour->prices->filter(function ($p) use ($catId, $tourDate) {
                                return $p->category_id == $catId && $p->isValidForDate($tourDate);
                            })->first();

                            if (!$priceModel) {
                                $priceModel = $tour->prices->where('category_id', $catId)
                                    ->whereNull('valid_from')->whereNull('valid_until')
                                    ->first();
                            }
                            if (!$priceModel) {
                                $priceModel = $tour->prices->where('category_id', $catId)->first();
                            }

                            $priceVal = $priceModel ? (float)$priceModel->price : 0.0;

                            $expandedCats[] = [
                                'category_id' => $catId,
                                'quantity' => $qty,
                                'price' => $priceVal,
                                'name' => $priceModel ? ($priceModel->category->getTranslatedName() ?? 'Category') : 'Category',
                            ];
                        }
                    }
                }
            }

            // Create CartItem
            $cart->items()->create([
                'tour_id' => $itemData['tour_id'] ?? null,
                'schedule_id' => $itemData['schedule_id'] ?? null,
                'tour_language_id' => $itemData['tour_language_id'] ?? null,
                'tour_date' => $itemData['tour_date'] ?? null,
                'hotel_id' => $itemData['hotel_id'] ?? null,
                'meeting_point_id' => $itemData['meeting_point_id'] ?? $itemData['selected_meeting_point'] ?? null, // handle both keys if inconsistent
                'is_other_hotel' => $itemData['is_other_hotel'] ?? false,
                'other_hotel_name' => $itemData['other_hotel_name'] ?? null,
                'categories' => $expandedCats,
                'is_active' => true,
            ]);
        }
    }
}
