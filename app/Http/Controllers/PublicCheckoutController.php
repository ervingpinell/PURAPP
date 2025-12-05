<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Policy;
use App\Models\PolicySection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Support\Str;
use App\Services\Policies\PolicySnapshotService;

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
        if (!$userId) {
            return redirect()->route('login');
        }

        $cart = $this->findActiveCartForUser($userId);
        $itemsCount = $cart ? $cart->items()->count() : 0;

        if (!$cart || $itemsCount === 0) {
            return redirect()->route('public.carts.index')
                ->with('error', __('adminlte::adminlte.emptyCart'));
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
        $freeCancelUntil = $this->computeFreeCancelUntil($cart);

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
        if (!$userId) return redirect()->route('login');

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
            return redirect()->route('public.carts.index')
                ->with('error', __('adminlte::adminlte.emptyCart'));
        }

        // Terms are now accepted on the payment page, so we skip validation and recording here.

        // 🔄 NUEVO FLUJO: Guardar snapshot del carrito en sesión (NO crear bookings aún)
        return $this->processCartSnapshot($request, $cart);
    }

    /**
     * Process cart snapshot and redirect to payment
     * Bookings will be created AFTER successful payment
     */
    private function processCartSnapshot(Request $request, Cart $cart)
    {
        $user = Auth::user();
        $notes = trim((string) ($request->input('notes') ?? session('checkout_notes', '')));

        // Reload cart with all relationships
        $cart->load(['items.tour.prices.category', 'items.schedule', 'items.language', 'items.hotel', 'items.meetingPoint']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('public.carts.index')
                ->with('error', __('carts.messages.cart_empty'));
        }

        if ($cart->isExpired()) {
            return redirect()->route('public.carts.index')
                ->with('error', __('carts.messages.cart_expired'));
        }

        // Validate capacity for all items
        $capacityService = app(\App\Services\Bookings\BookingCapacityService::class);
        $groups = $cart->items->groupBy(fn($i) => $i->tour_id . '_' . $i->tour_date . '_' . $i->schedule_id);

        foreach ($groups as $items) {
            $first = $items->first();
            $tour = $first->tour;
            $tourDate = $first->tour_date;
            $scheduleId = $first->schedule_id;

            $schedule = $tour->schedules()
                ->where('schedules.schedule_id', $scheduleId)
                ->where('schedules.is_active', true)
                ->wherePivot('is_active', true)
                ->first();

            if (!$schedule) {
                return redirect()->route('public.carts.index')
                    ->with('error', __("carts.messages.schedule_unavailable"));
            }

            // Calculate total pax
            $totalPax = $items->sum(function ($item) {
                return collect($item->categories ?? [])->sum('quantity');
            });

            $remaining = $capacityService->remainingCapacity(
                $tour,
                $schedule,
                $tourDate,
                excludeBookingId: null,
                countHolds: true,
                excludeCartId: (int) $cart->cart_id
            );

            if ($totalPax > $remaining) {
                return redirect()->route('public.carts.index')
                    ->with('error', __("m_bookings.messages.limited_seats_available", [
                        'available' => $remaining,
                        'tour' => $tour->getTranslatedName(),
                        'date' => \Carbon\Carbon::parse($tourDate)->translatedFormat('M d, Y')
                    ]));
            }
        }

        // Get promo code if exists
        $promoCodeValue = $request->input('promo_code') ?? $request->session()->get('public_cart_promo.code');

        Log::info('Checkout Process - Promo Code Value:', ['value' => $promoCodeValue, 'source' => $request->input('promo_code') ? 'input' : 'session']);

        $promoCode = null;

        if ($promoCodeValue) {
            $clean = \App\Models\PromoCode::normalize($promoCodeValue);
            $promoCode = \App\Models\PromoCode::whereRaw("UPPER(TRIM(REPLACE(code, ' ', ''))) = ?", [$clean])
                ->first();

            Log::info('Checkout Process - Promo Code Found:', ['found' => (bool)$promoCode, 'id' => $promoCode?->id]);

            if ($promoCode && method_exists($promoCode, 'isValidToday') && !$promoCode->isValidToday()) {
                Log::info("Promo code {$promoCode->code} rejected: not valid today");
                $promoCode = null;
            }
            if ($promoCode && method_exists($promoCode, 'hasRemainingUses') && !$promoCode->hasRemainingUses()) {
                Log::info("Promo code {$promoCode->code} rejected: no remaining uses");
                $promoCode = null;
            }
            if ($promoCode) {
                Log::info('Checkout Process - Promo Code Accepted:', ['code' => $promoCode->code]);
            }
        }

        // Get full promo session data
        $promoSession = $request->session()->get('public_cart_promo');

        // Calculate subtotal for snapshot metadata
        $subtotal = $cart->items->sum(function ($item) {
            return collect($item->categories ?? [])->sum(function ($cat) {
                return ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0));
            });
        });

        // Create cart snapshot for session
        $cartSnapshot = [
            'user_id' => $user->user_id,
            'cart_id' => $cart->cart_id,
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
            'total' => $subtotal,
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

        // Login the user if not already logged in
        if (!Auth::check() || Auth::id() !== $booking->user_id) {
            Auth::loginUsingId($booking->user_id);
        }

        // Create or get existing cart for this user
        $cart = Cart::firstOrCreate(
            ['user_id' => $booking->user_id],
            [
                'is_active' => true,
                'expires_at' => now()->addMinutes((int) setting('cart.expiration_minutes', 30))
            ]
        );

        // Always ensure cart is active and has correct expiration based on current setting
        $cart->is_active = true;
        // We force update expiration to match current setting, ensuring user sees the configured time
        // This fixes the issue where changing the setting didn't update existing carts
        $cart->expires_at = now()->addMinutes((int) setting('cart.expiration_minutes', 30));
        $cart->save();

        // Store booking_id in session for later use
        session(['pending_booking_payment' => $booking->booking_id]);

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
            'cart_id' => $cart->cart_id,
            'user_id' => $booking->user_id,
            'is_active' => true,
            'expires_at' => $cart->expires_at,
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
        ]);
    }
}
