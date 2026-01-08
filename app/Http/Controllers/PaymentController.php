<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Tour;
use App\Models\PromoCode;
use App\Services\PaymentService;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use App\Models\PolicySection;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Show payment page
     */
    public function show(Request $request)
    {
        // Obtener snapshot del carrito desde sesión o desde bookingverdad para importe
        $cartSnapshot = session('cart_snapshot');
        $bookingId = $request->input('booking_id');
        $booking = null;

        // Si viene un booking_id (link de pago), intentamos reconstruir el snapshot desde la reserva
        if ($bookingId && !$cartSnapshot) {
            $booking = Booking::with(['detail', 'tour', 'user', 'detail.schedule'])->find($bookingId);

            if ($booking && ($booking->status === 'pending' || $booking->status === 'confirmed')) {
                // 🔒 STRICT EXPIRATION CHECK: Prevent "Ghost Carts"
                // If booking is pending and older than allowed time, do not allow payment.
                $timeoutMinutes = config('booking.payment_completion_timeout_minutes', 20);

                // We add a small grace period (e.g. 5 min) for the user to finish the process if they are already there,
                // but if they are coming back fresh to an old booking, it should be blocked.
                // However, user requested strict "expire and do not allow continue".
                if ($booking->status === 'pending' && $booking->created_at->addMinutes($timeoutMinutes)->isPast()) {
                    return redirect()->route(app()->getLocale() . '.home', ['cart_expired' => 1])
                        ->with('cart_expired', true);
                }

                // Reconstruir snapshot desde el booking
                $cartSnapshot = [
                    'cart_id' => null, // No hay carrito real asociado en este flujo directo
                    'user_id' => $booking->user_id,
                    'items' => [
                        [
                            'tour_id' => $booking->tour_id,
                            'tour_date' => $booking->tour_date,
                            'schedule_id' => $booking->detail?->schedule_id,
                            'language_id' => $booking->detail?->tour_language_id, // Add language for PaymentService
                            'hotel_id' => $booking->detail?->hotel_id,
                            'meeting_point_id' => $booking->detail?->meeting_point_id,
                            'categories' => $booking->detail?->categories ?? [], // Asumiendo que guardamos categories en detail o booking
                            'quantity' => $booking->pax,
                            'price' => $booking->total, // Usar 'total' en lugar de 'total_amount'
                            'tax_breakdown' => $booking->detail?->taxes_breakdown ?? [],
                        ]
                    ],
                    'subtotal' => $booking->detail?->total ?? $booking->total, // Usar detail->total como subtotal
                    'total' => $booking->total, // Campo correcto es 'total'
                    'currency' => $booking->currency ?? config('payment.default_currency', 'USD'),
                    'created_at' => now()->toIso8601String(),
                    'is_booking_payment' => true, // Flag para saber que es pago de reserva existente
                    'booking_id' => $booking->booking_id
                ];

                // Guardar en sesión para que el initiate() lo encuentre
                session(['cart_snapshot' => $cartSnapshot]);
            }
        }

        if (!$cartSnapshot || empty($cartSnapshot['items'])) {
            return redirect()->route(app()->getLocale() . '.home');
        }

        // Tiempo para completar el pago
        $timeoutMinutes = config('booking.payment_completion_timeout_minutes', 20);

        // Momento de inicio del pago
        $paymentStartTime = session('payment_start_time');

        // 🔒 STRICT CHECK: If we are paying for an existing booking, use its created_at time
        // This prevents "Ghost Carts" where the session timer resets but the inventory hold should have expired.
        if (!empty($cartSnapshot['booking_id'])) {
            $existingBooking = \App\Models\Booking::find($cartSnapshot['booking_id']);
            if ($existingBooking) {
                // Use the REAL booking creation time, not the session start time
                $paymentStartTime = $existingBooking->created_at;

                // Update session to match reality (so visual timer is correct)
                session(['payment_start_time' => $paymentStartTime]);
            }
        }

        if (!$paymentStartTime) {
            $paymentStartTime = now();
            session(['payment_start_time' => $paymentStartTime]);
        }

        $expiresAt = \Carbon\Carbon::parse($paymentStartTime)
            ->addMinutes($timeoutMinutes);

        // Si ya se venció la sesión de pago
        if (now()->greaterThan($expiresAt)) {
            session()->forget(['cart_snapshot', 'payment_start_time', 'cart_reservation_token']);
            return redirect()->route(app()->getLocale() . '.home', ['cart_expired' => 1])
                ->with('cart_expired', true);
        }

        // Calcular total con promo si existe       // Total SIEMPRE desde el snapshot
        // ========================
        $total        = $this->calculateTotalFromSnapshot($cartSnapshot);
        $currency     = config('payment.default_currency', 'USD');
        $defaultGateway = config('payment.default_gateway', 'stripe');

        // ========================
        // Gateways habilitados
        // ========================
        $enabledGateways = $this->getEnabledGateways();

        // Calculate free cancellation deadline
        $freeCancelUntil = null;
        if (!empty($cartSnapshot['items'])) {
            // Find earliest tour date
            $earliestDate = null;
            foreach ($cartSnapshot['items'] as $item) {
                if (!empty($item['tour_date'])) {
                    $d = \Carbon\Carbon::parse($item['tour_date']);

                    // Try to get start time from schedule
                    $startTime = null;
                    if (!empty($item['schedule']) && $item['schedule'] instanceof \App\Models\Schedule) {
                        $startTime = $item['schedule']->start_time;
                    } elseif (!empty($item['schedule_id'])) {
                        // If model isn't hydrated yet (though we just added hydration, logic order matters), fetch it
                        $sched = \App\Models\Schedule::find($item['schedule_id']);
                        if ($sched) {
                            $startTime = $sched->start_time;
                        }
                    }

                    if ($startTime) {
                        $timeParts = explode(':', $startTime);
                        if (count($timeParts) >= 2) {
                            $d->setTime((int)$timeParts[0], (int)$timeParts[1], 0);
                        }
                    }

                    if (!$earliestDate || $d->lt($earliestDate)) {
                        $earliestDate = $d;
                    }
                }
            }

            if ($earliestDate) {
                $cutoffHours = config('booking.free_cancellation_hours', 24);
                $freeCancelUntil = $earliestDate->copy()->subHours($cutoffHours);
            }
        }

        // Fetch active policies for terms modal (using helper to format blocks)
        $locale = app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'es');

        $policyResult = $this->buildPolicyBlocksFromDB($locale, $fallback);
        $policyBlocks = $policyResult['blocks'];
        $versions = $policyResult['versions'];

        $termsVersion = $versions['terms'] ?? 'v1';
        $privacyVersion = $versions['privacy'] ?? 'v1';

        // Get cart object for display if needed (optional, view uses items array mostly)
        $cart = null; // We are using snapshot items

        return view('public.checkout-payment', [
            'total'           => $total,
            'currency'        => $currency,
            'defaultGateway'  => $defaultGateway,
            'enabledGateways' => $enabledGateways,
            'stripeKey'       => config('payment.gateways.stripe.publishable_key'),
            'items'           => collect($cartSnapshot['items'])->map(function ($item) {
                // Determine tour_id (support both keys for robust handling)
                $tourId = $item['tour_id'] ?? $item['tour'] ?? null;
                // If it's an object, use it; if ID, fetch it.
                if (is_object($tourId)) {
                    $item['tour'] = $tourId;
                } elseif ($tourId) {
                    $item['tour'] = \App\Models\Tour::with('translations')->find($tourId);
                }

                // Schedule
                if (empty($item['schedule']) && !empty($item['schedule_id'])) {
                    $item['schedule'] = \App\Models\Schedule::find($item['schedule_id']);
                }

                // Language
                if (empty($item['language']) && !empty($item['language_id'])) {
                    $item['language'] = \App\Models\TourLanguage::find($item['language_id']);
                }

                // Hotel
                if (empty($item['hotel']) && !empty($item['hotel_id'])) {
                    $item['hotel'] = \App\Models\HotelList::find($item['hotel_id']);
                }

                // Meeting Point
                if (empty($item['meetingPoint']) && !empty($item['meeting_point_id'])) {
                    $item['meetingPoint'] = \App\Models\MeetingPoint::find($item['meeting_point_id']);
                }

                return $item;
            }),
            'expiresAt'       => $expiresAt, // Pass expiration time for countdown
            'cart'            => $cart,
            'freeCancelUntil' => $freeCancelUntil,
            'policyBlocks'    => $policyBlocks,
            'termsVersion'    => $termsVersion,
            'privacyVersion'  => $privacyVersion,
        ]);
    }

    /**
     * Show payment page by token (no authentication required)
     */
    public function showByToken(string $token)
    {
        // Validate token format (64-char hex)
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            abort(404);
        }

        // Find booking by token
        $booking = Booking::where('payment_token', $token)
            ->with(['detail', 'tour', 'user', 'detail.schedule', 'payments'])
            ->first();

        if (!$booking) {
            abort(404);
        }

        // Check if already paid
        $isPaid = $booking->payments()
            ->where('status', 'completed')
            ->exists();

        if ($isPaid) {
            return view('public.payment-already-paid', compact('booking'));
        }

        // Check expiration
        $expirationHours = (int) \App\Models\Setting::getValue('booking.payment_link_expiration_hours', 2);
        $createdAt = $booking->payment_token_created_at ?? $booking->created_at; // Fallback for old bookings

        if ($createdAt->addHours($expirationHours)->isPast()) {
            abort(410, 'Payment link has expired.');
        }

        // Reconstruct cart snapshot from booking
        $cartSnapshot = [
            'cart_id' => null,
            'user_id' => $booking->user_id,
            'items' => [
                [
                    'tour_id' => $booking->tour_id,
                    'tour_date' => $booking->tour_date,
                    'schedule_id' => $booking->detail?->schedule_id,
                    'language_id' => $booking->detail?->tour_language_id,
                    'hotel_id' => $booking->detail?->hotel_id,
                    'meeting_point_id' => $booking->detail?->meeting_point_id,
                    'categories' => $booking->detail?->categories ?? [],
                    'quantity' => $booking->pax,
                    'price' => $booking->total,
                    'tax_breakdown' => $booking->detail?->taxes_breakdown ?? [],
                ]
            ],
            'subtotal' => $booking->detail?->total ?? $booking->total,
            'total' => $booking->total,
            'currency' => $booking->currency ?? config('payment.default_currency', 'USD'),
            'created_at' => now()->toIso8601String(),
            'is_booking_payment' => true,
            'booking_id' => $booking->booking_id,
            'payment_token' => $token, // Store token for initiate
        ];

        // Save to session
        session(['cart_snapshot' => $cartSnapshot]);

        Log::info('Payment accessed via token', [
            'token_preview' => substr($token, 0, 8) . '...',
            'booking_id' => $booking->booking_id,
            'total' => $booking->total,
        ]);

        // Reuse the same payment page logic
        $timeoutMinutes = config('booking.payment_completion_timeout_minutes', 20);
        $paymentStartTime = session('payment_start_time');

        if (!$paymentStartTime) {
            $paymentStartTime = now();
            session(['payment_start_time' => $paymentStartTime]);
        }

        $expiresAt = \Carbon\Carbon::parse($paymentStartTime)->addMinutes($timeoutMinutes);

        $total = $this->calculateTotalFromSnapshot($cartSnapshot);
        $currency = config('payment.default_currency', 'USD');
        $defaultGateway = config('payment.default_gateway', 'stripe');

        // Get enabled gateways (same logic as show())
        $enabledGateways = $this->getEnabledGateways();

        // Reconstruct items for display (same logic as show() method)
        $items = collect([
            [
                'tour'         => $booking->tour,
                'tour_date'    => $booking->tour_date,
                'categories'   => $booking->detail?->categories ?? [],
                'schedule'     => $booking->detail?->schedule,
                'language'     => $booking->detail?->tourLanguage,
                'hotel'        => $booking->detail?->hotel_id ? \App\Models\HotelList::find($booking->detail->hotel_id) : null,
                'meetingPoint' => $booking->detail?->meeting_point_id ? \App\Models\MeetingPoint::find($booking->detail->meeting_point_id) : null,
                'addons'       => [],
                'notes'        => $booking->notes,
                'duration'     => $booking->tour?->length,
                'guide'        => null,
            ]
        ]);

        // Calculate free cancellation deadline
        $freeCancelUntil = null;
        if ($booking->detail?->tour_date) {
            $cutoffHours = config('booking.free_cancellation_hours', 24);
            $freeCancelUntil = \Carbon\Carbon::parse($booking->detail->tour_date)->subHours($cutoffHours);
        }

        // Fetch active policies for terms modal (using helper to format blocks)
        $locale = app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'es');

        $policyResult = $this->buildPolicyBlocksFromDB($locale, $fallback);
        $policyBlocks = $policyResult['blocks'];
        $versions = $policyResult['versions'];

        $termsVersion = $versions['terms'] ?? 'v1';

        return view('public.checkout-payment', [
            'total' => $total,
            'currency' => $currency,
            'defaultGateway' => $defaultGateway,
            'enabledGateways' => $enabledGateways,
            'expiresAt' => $expiresAt,
            'timeoutMinutes' => $timeoutMinutes,
            'items' => $items,
            'cart' => null,
            'freeCancelUntil' => $freeCancelUntil,
            'booking' => $booking, // Pass booking for display
            'stripeKey' => config('payment.gateways.stripe.publishable_key'),
            'policyBlocks' => $policyBlocks,
            'termsVersion' => $termsVersion,
        ]);
    }

    /**
     * Calculate total from cart snapshot
     * Uses the prices already stored in the snapshot (calculated with correct date)
     */
    private function calculateTotalFromSnapshot(array $cartSnapshot): float
    {
        $total = 0;

        foreach ($cartSnapshot['items'] as $item) {
            // Use prices from the snapshot - they were calculated with the correct tour_date
            foreach ($item['categories'] as $cat) {
                $quantity = (int)($cat['quantity'] ?? 0);
                $price = (float)($cat['price'] ?? 0);

                // If tax_breakdown exists, use the total from there
                if (isset($cat['tax_breakdown']) && is_array($cat['tax_breakdown'])) {
                    $total += (float)($cat['tax_breakdown']['total'] ?? 0);
                } else {
                    // Fallback: price * quantity
                    $total += $price * $quantity;
                }
            }
        }

        // Apply promo code from snapshot if available (preferred)
        if (!empty($cartSnapshot['promo_snapshot'])) {
            $promoData = $cartSnapshot['promo_snapshot'];
            $adjustment = (float)($promoData['adjustment'] ?? 0);
            $operation = $promoData['operation'] ?? 'subtract';

            $op = $operation === 'add' ? 1 : -1;
            $total = max(0, round($total + $op * $adjustment, 2));

            Log::info('Payment Total - Applied Snapshot Promo:', [
                'code' => $promoData['code'] ?? 'N/A',
                'adjustment' => $adjustment,
                'operation' => $operation,
                'new_total' => $total
            ]);
        }
        // Fallback: Apply promo code by ID lookup
        elseif (!empty($cartSnapshot['promo_code_id'])) {
            $promo = PromoCode::find($cartSnapshot['promo_code_id']);

            if ($promo) {
                $discount = 0;

                if ($promo->discount_percent > 0) {
                    $discount = $total * ($promo->discount_percent / 100);
                } elseif ($promo->discount_amount > 0) {
                    $discount = $promo->discount_amount;
                }

                $total = $promo->operation === 'add'
                    ? $total + $discount
                    : $total - $discount;

                Log::info('Payment Total - Applied DB Promo:', [
                    'code' => $promo->code,
                    'discount' => $discount,
                    'operation' => $promo->operation,
                    'new_total' => $total
                ]);
            }
        }

        return max($total, 0);
    }

    /**
     * Initiate payment
     */
    // App\Http\Controllers\PaymentController.php

    public function initiate(Request $request)
    {
        Log::info('Payment Initiate - Request received', [
            'gateway' => $request->input('gateway'),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
        ]);

        // Validate request input
        $validated = $request->validate([
            'gateway' => 'required|string|in:stripe,paypal,alignet',
        ]);

        try {
            $cartSnapshot = session('cart_snapshot');

            if (!$cartSnapshot || empty($cartSnapshot['items'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cart data found',
                ], 400);
            }

            // 1) Gateway solicitado
            $gateway = $validated['gateway'];

            // 2) Total + moneda
            $total    = $this->calculateTotalFromSnapshot($cartSnapshot);
            $currency = config('payment.default_currency', 'USD');

            // 3) Get booking_id if this is a booking payment
            $bookingId = $cartSnapshot['booking_id'] ?? null;

            // 4) Try to find and reuse existing payment intent
            $payment = $this->findOrCreatePaymentIntent($cartSnapshot, $gateway, $total, $currency, $bookingId);

            // If payment already has a gateway intent ID, return it immediately (don't call gateway again)
            if ($payment->gateway_payment_intent_id) {
                $existingResponse = $payment->gateway_response ?? [];

                Log::info('Reusing existing payment intent', [
                    'payment_id' => $payment->payment_id,
                    'gateway' => $gateway,
                    'booking_id' => $bookingId,
                    'was_recently_created' => $payment->wasRecentlyCreated ?? false,
                ]);

                // Store payment ID in session for guest status polling
                session(['guest_payment_id' => $payment->payment_id]);

                $redirectUrl = $existingResponse['redirect_url'] ?? $existingResponse['approval_url'] ?? null;
                if ($gateway === 'alignet') {
                    $redirectUrl = route('payment.alignet', ['payment' => $payment->payment_id]);
                }

                return response()->json([
                    'success'        => true,
                    'client_secret'  => $existingResponse['client_secret'] ?? null,
                    'redirect_url'   => $redirectUrl,
                    'approval_url'   => $redirectUrl,
                    'payment_id'     => $payment->payment_id,
                    'reused'         => true,
                ]);
            }

            // Log whether we're creating a new payment or updating an existing one
            Log::info('Processing payment intent', [
                'payment_id' => $payment->payment_id,
                'is_new' => $payment->wasRecentlyCreated ?? false,
                'gateway' => $gateway,
            ]);

            // 5) Create new intent/order in the selected gateway
            $gatewayManager = app(\App\Services\PaymentGateway\PaymentGatewayManager::class);

            try {
                $gatewayDriver = $gatewayManager->driver($gateway);
            } catch (\App\Services\PaymentGateway\Exceptions\GatewayNotEnabledException $e) {
                return response()->json([
                    'success' => false,
                    'message' => "The {$gateway} payment gateway is not enabled",
                ], 400);
            } catch (\App\Services\PaymentGateway\Exceptions\GatewayNotImplementedException $e) {
                return response()->json([
                    'success' => false,
                    'message' => "The {$gateway} payment gateway is not available yet",
                ], 400);
            }

            // ==========================================
            // 📝 TERMS ACCEPTANCE AUDIT
            // ==========================================
            // For Stripe, we allow initiation without terms (setup phase).
            // For Redirect gateways (PayPal), we require terms immediately as initiate = action.
            if ($gateway !== 'stripe' && !$request->input('terms_accepted')) {
                return response()->json([
                    'success' => false,
                    'message' => __('m_checkout.accept.error'),
                ], 422);
            }

            // Record terms acceptance if provided (or required)
            if ($request->input('terms_accepted')) {
                try {
                    $locale = app()->getLocale();
                    $fallback = (string) config('app.fallback_locale', 'es');

                    // Re-build blocks to get current versions/snapshot
                    $dbPack = $this->buildPolicyBlocksFromDB($locale, $fallback);
                    $blocks = $dbPack['blocks'] ?? [];
                    $versions = $dbPack['versions'] ?? ['terms' => 'v1', 'privacy' => 'v1'];

                    $termsVersion = $versions['terms'] ?? 'v1';
                    $privacyVersion = $versions['privacy'] ?? 'v1';

                    // Generate snapshot hash
                    $normalized = is_array($blocks)
                        ? preg_replace('/\s+/', ' ', json_encode($blocks, JSON_UNESCAPED_UNICODE) ?: '')
                        : preg_replace('/\s+/', ' ', (string) implode('|', (array) $blocks));
                    $sha = hash('sha256', (string) $normalized);

                    DB::table('terms_acceptances')->updateOrInsert(
                        // Match: by cart_ref (if exists) or booking_ref (if exists)
                        [
                            'cart_ref' => $cartSnapshot['cart_id'] ?? null,
                            'booking_ref' => $bookingId ?? null,
                        ],
                        // Update/Insert
                        [
                            'user_id'           => Auth::id() ?? $cartSnapshot['user_id'] ?? null,
                            'accepted_at'       => now(),
                            'terms_version'     => $termsVersion,
                            'privacy_version'   => $privacyVersion,
                            'policies_snapshot' => json_encode($blocks, JSON_UNESCAPED_UNICODE),
                            'policies_sha256'   => $sha,
                            'ip_address'        => $request->ip(),
                            'user_agent'        => (string) $request->userAgent(),
                            'locale'            => $locale,
                            'timezone'          => config('app.timezone'),
                            'consent_source'    => 'payment_page',
                            'referrer'          => $request->headers->get('referer'),
                            'updated_at'        => now(),
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to record terms acceptance in payment initiate', [
                        'error' => $e->getMessage(),
                        'booking_id' => $bookingId
                    ]);
                }
            }

            // Get user info (from auth or cart snapshot for token-based payments)
            $userId = Auth::id() ?? $cartSnapshot['user_id'] ?? null;
            $userEmail = Auth::user()?->email ?? null;

            // Name handling
            $firstName = '';
            $lastName = '';

            // If no user email from auth, try to get from booking
            if ($bookingId) {
                // Determine booking (ensure loaded)
                $booking = \App\Models\Booking::with('user')->find($bookingId); // Use locally scoped var
                if ($booking) {
                    $userEmail = $userEmail ?? $booking->user?->email ?? $booking->customer_email;
                    $firstName = $booking->customer_name ?? '';
                    $lastName  = $booking->customer_lastname ?? '';
                }
            }

            // Fallback to Auth User name splitting if names are still empty
            if (empty($firstName) && Auth::check()) {
                $fullName = trim(Auth::user()->name);
                // Split by first space
                $parts = explode(' ', $fullName, 2);
                $firstName = $parts[0];
                $lastName  = $parts[1] ?? '';
            }

            // Final fallback defaults to satisfy gateways requiring non-empty strings
            if (empty($firstName)) $firstName = 'Guest';
            if (empty($lastName))  $lastName  = '-'; // Placeholder for single-name users

            // Prepare Address Fields
            $address = '';
            $city = '';
            $state = '';
            $zip = '';
            $country = 'CR';
            $phone = '';

            if ($bookingId) {
                // Booking Logic: Use booking user or booking fields
                $booking = \App\Models\Booking::with('user')->find($bookingId);
                if ($booking) {
                    $u = $booking->user;
                    if ($u) {
                        $address = $u->address ?? '';
                        $city    = $u->city ?? '';
                        $state   = $u->state ?? '';
                        $zip     = $u->zip ?? '';
                        $country = $u->country ?? 'CR';
                        $phone   = $u->phone ?? '';
                    }
                    // Fallback to booking fields if user fields empty? (Only if we stored them in booking, but generally we rely on user link)
                }
            } elseif (Auth::check()) {
                // Auth User Logic
                $u = Auth::user();
                $address = $u->address ?? '';
                $city    = $u->city ?? '';
                $state   = $u->state ?? '';
                $zip     = $u->zip ?? '';
                $country = $u->country ?? 'CR';
                $phone   = $u->phone ?? '';
            } else {
                // GUEST LOGIC: Retrieve from Session
                $firstName = session('guest_user_first_name', '');
                $lastName  = session('guest_user_last_name', '');

                // Fallback to snapshot if session empty
                if (empty($firstName)) $firstName = $cartSnapshot['guest_first_name'] ?? '';
                if (empty($lastName))  $lastName  = $cartSnapshot['guest_last_name'] ?? '';

                $address = session('guest_user_address', '');
                $city    = session('guest_user_city', '');
                $state   = session('guest_user_state', '');
                $zip     = session('guest_user_zip', '');
                $country = session('guest_user_country', 'CR');
                $phone   = session('guest_user_phone', '');
            }

            // FALLBACK LOGIC (Split Name if separate fields missing)
            if (empty($firstName) && empty($lastName)) {
                $gName = $cartSnapshot['guest_name'] ?? session('guest_user_name');
                if ($gName) {
                    $parts = explode(' ', trim($gName), 2);
                    $firstName = $parts[0];
                    $lastName  = $parts[1] ?? '-';
                }
            }
            if (empty($userEmail)) {
                $userEmail = $cartSnapshot['guest_email'] ?? session('guest_user_email');
            }

            // Fallback defaults if still empty (Alignet requires non-empty)
            if (empty($address)) $address = 'Not Provided';
            if (empty($city))    $city    = 'Not Provided';
            if (empty($state))   $state   = 'Not Provided';
            if (empty($zip))     $zip     = '00000';

            $intentData = [
                'amount'        => $total,
                'currency'      => $currency,
                'user_id'       => $userId,
                'user_email'    => $userEmail,
                'customer_first_name' => $firstName,
                'customer_last_name'  => $lastName,
                'customer_address'    => $address,
                'customer_city'       => $city,
                'customer_state'      => $state,
                'customer_zip'        => $zip,
                'customer_country'    => $country,
                'customer_phone'      => $phone,
                'description'   => 'Cart checkout',
                'receipt_email' => $userEmail,
                'options'       => [
                    'return_url' => route('payment.return'),
                    'cancel_url' => route('payment.cancel'),
                ],
                // 🔥 CRITICAL: Pass payment_id and booking_id for Alignet to store in reserved fields
                'payment_id'    => $payment->payment_id,
                'booking_id'    => $bookingId,
            ];
            $result = $gatewayDriver->createPaymentIntent($intentData);

            // 6) Update payment with gateway intent data
            $payment->update([
                'gateway_payment_intent_id' => $result->paymentIntentId,
                'status'                    => 'processing',
                'gateway_response'          => $result->toArray(),
            ]);

            Log::info('Gateway intent created successfully', [
                'gateway' => $gateway,
                'payment_id' => $payment->payment_id,
                'booking_id' => $bookingId,
            ]);

            // Store payment ID in session for guest status polling
            session(['guest_payment_id' => $payment->payment_id]);

            $redirectUrl = $result->redirectUrl;
            if ($gateway === 'alignet') {
                $redirectUrl = route('payment.alignet', ['payment' => $payment->payment_id]);
            }

            return response()->json([
                'success'       => true,
                'client_secret' => $result->clientSecret,
                'redirect_url'  => $redirectUrl,
                'approval_url'  => $redirectUrl, // Alias for backward compatibility
                'payment_id'    => $payment->payment_id,
                'reused'        => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Initiate Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record terms acceptance explicitly (called via AJAX before Stripe confirmation)
     */
    public function recordTerms(Request $request)
    {
        $request->validate([
            'terms_accepted' => 'required|accepted'
        ]);

        try {
            $cartSnapshot = session('cart_snapshot');
            if (!$cartSnapshot) {
                return response()->json(['success' => false, 'message' => 'No session'], 400);
            }

            $bookingId = $cartSnapshot['booking_id'] ?? null;

            $locale = app()->getLocale();
            $fallback = (string) config('app.fallback_locale', 'es');

            $dbPack = $this->buildPolicyBlocksFromDB($locale, $fallback);
            $blocks = $dbPack['blocks'] ?? [];
            $versions = $dbPack['versions'] ?? ['terms' => 'v1', 'privacy' => 'v1'];

            $termsVersion = $versions['terms'] ?? 'v1';
            $privacyVersion = $versions['privacy'] ?? 'v1';

            $normalized = is_array($blocks)
                ? preg_replace('/\s+/', ' ', json_encode($blocks, JSON_UNESCAPED_UNICODE) ?: '')
                : preg_replace('/\s+/', ' ', (string) implode('|', (array) $blocks));
            $sha = hash('sha256', (string) $normalized);

            DB::table('terms_acceptances')->updateOrInsert(
                [
                    'cart_ref' => $cartSnapshot['cart_id'] ?? null,
                    'booking_ref' => $bookingId ?? null,
                ],
                [
                    'user_id'           => Auth::id() ?? $cartSnapshot['user_id'] ?? null,
                    'accepted_at'       => now(),
                    'terms_version'     => $termsVersion,
                    'privacy_version'   => $privacyVersion,
                    'policies_snapshot' => json_encode($blocks, JSON_UNESCAPED_UNICODE),
                    'policies_sha256'   => $sha,
                    'ip_address'        => $request->ip(),
                    'user_agent'        => (string) $request->userAgent(),
                    'locale'            => $locale,
                    'timezone'          => config('app.timezone'),
                    'consent_source'    => 'payment_page_ajax',
                    'referrer'          => $request->headers->get('referer'),
                    'updated_at'        => now(),
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to record terms acceptance via AJAX', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Find existing payment intent or create a new one
     * 
     * @param array $cartSnapshot
     * @param string $gateway
     * @param float $total
     * @param string $currency
     * @param int|null $bookingId
     * @return \App\Models\Payment
     */
    protected function findOrCreatePaymentIntent($cartSnapshot, $gateway, $total, $currency, $bookingId = null)
    {
        // First, fail any expired pending/processing payments for this booking+gateway
        if ($bookingId) {
            Payment::where('booking_id', $bookingId)
                ->where('gateway', $gateway)
                ->whereIn('status', ['pending', 'processing'])
                ->where('expires_at', '<', now())
                ->update(['status' => 'failed']);
        }

        // Get user ID from auth or snapshot
        $userId = Auth::id() ?? $cartSnapshot['user_id'] ?? null;
        $sessionId = session()->getId();

        // Try to find an existing active payment intent
        // We specifically check metadata->session_id for guests to prevent collisions
        $query = Payment::where('gateway', $gateway)
            ->whereIn('status', ['pending', 'processing'])
            ->where('expires_at', '>', now())
            ->where(function ($q) use ($total) {
                // Fuzzy match for float amount or exact match for string
                $q->where('amount', $total)
                    ->orWhereRaw('ABS(amount - ?) < 0.01', [$total]);
            })
            ->where('currency', $currency);

        if ($bookingId) {
            $query->where('booking_id', $bookingId);
        } else {
            // If no booking ID, strict check on user/session
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                // Guest: Must match session_id in metadata to avoid hijacking
                $query->where('user_id', null)
                    ->where('metadata->session_id', $sessionId);
            }
        }

        $existingPayment = $query->orderByDesc('created_at')->first();

        if ($existingPayment) {
            Log::info('Found existing valid payment intent', [
                'payment_id' => $existingPayment->payment_id,
                'gateway' => $gateway,
                'booking_id' => $bookingId,
                'has_intent_id' => !empty($existingPayment->gateway_payment_intent_id),
            ]);

            return $existingPayment;
        }

        // No existing payment found, create a new one
        $expiresAt = now()->addMinutes(30);

        $payment = Payment::create([
            'user_id'    => $userId,
            'booking_id' => $bookingId,
            'gateway'    => $gateway,
            'amount'     => $total,
            'currency'   => $currency,
            'status'     => 'pending',
            'expires_at' => $expiresAt,
            'metadata'   => [
                'cart_snapshot' => $cartSnapshot,
                'created_from'  => 'checkout',
                'session_id'    => $sessionId, // Important for guest retries
            ],
        ]);

        Log::info('Created new payment intent', [
            'payment_id' => $payment->payment_id,
            'gateway' => $gateway,
            'booking_id' => $bookingId,
            'session_id' => $sessionId,
            'amount' => $total
        ]);

        return $payment;
    }

    /**
     * Extract PayPal approval URL
     */
    private function extractApproveUrl(array $intent)
    {
        $links = $intent['links'] ?? [];

        foreach ($links as $l) {
            if (($l['rel'] ?? null) === 'approve') {
                return $l['href'];
            }
        }
        return null;
    }

    /**
     * Detect next action depending on gateway response
     */
    private function detectNextAction(array $intent)
    {
        if (!empty($intent['client_secret'])) {
            return 'stripe_confirm';
        }

        if (!empty($this->extractApproveUrl($intent))) {
            return 'redirect_to_gateway';
        }

        if (!empty($intent['redirect_url'])) {
            return 'redirect_to_gateway';
        }

        return 'unknown';
    }

    /**
     * Confirm payment
     */
    public function confirm(Request $request)
    {
        // Stripe sends 'payment_intent', but our code expects 'payment_intent_id'
        // Allow both
        $intentId = $request->input('payment_intent') ?? $request->input('payment_intent_id');

        if (!$intentId) {
            return back()->with('error', 'Missing payment intent ID');
        }

        try {
            // Find payment by intent ID (unique enough to not require user_id check for guests)
            $payment = Payment::where('gateway_payment_intent_id', $intentId)
                ->firstOrFail();

            $status = $this->paymentService->getPaymentStatus($payment);

            if ($status['status'] === 'succeeded') {
                $this->paymentService->handleSuccessfulPayment($payment, $status);

                $booking = Booking::find($payment->booking_id);
                $booking->load('user'); // Ensure user is loaded for guest check check
                session()->forget(['pending_booking_ids', 'guest_payment_id']);

                // Generate password setup URL for guests
                $passwordSetupUrl = null;

                if ($booking->user) {
                    $svc = app(\App\Services\Auth\PasswordSetupService::class);

                    \Illuminate\Support\Facades\Log::info('PaymentController Debug', [
                        'user_id' => $booking->user_id,
                        'needs_setup' => $svc->needsPasswordSetup($booking->user),
                        'password_len' => strlen($booking->user->password ?? ''),
                    ]);

                    if ($svc->needsPasswordSetup($booking->user)) {
                        try {
                            $tokenData = $svc->generateSetupToken($booking->user);
                            $passwordSetupUrl = route('password.setup.show', ['token' => $tokenData['plain_token']]);
                            \Illuminate\Support\Facades\Log::info('Generated Setup URL: ' . $passwordSetupUrl);
                        } catch (\Exception $e) {
                            Log::error('Failed to generate password setup token for confirmation view', [
                                'booking_id' => $booking->booking_id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    }
                }

                return view('public.payment-confirmation', compact('booking', 'passwordSetupUrl'));
            }

            return back()->with('error', 'Payment not successful');
        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error confirming payment');
        }
    }

    public function cancel(Request $request)
    {
        return redirect()->route('payment.show')->with('warning', 'Payment was cancelled.');
    }

    public function error(Request $request)
    {
        $errorMessage = $request->session()->get('error', __('m_checkout.payment.error_occurred'));
        return redirect()->route('public.carts.index')->with('error', $errorMessage);
    }

    public function status(Request $request, Payment $payment)
    {
        // Allow if user is owner OR if it matches the guest payment in session
        $isOwner = $payment->user_id === Auth::id();
        $isGuestOwner = session('guest_payment_id') == $payment->payment_id;

        if (!$isOwner && !$isGuestOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $status = $this->paymentService->getPaymentStatus($payment);

            return response()->json([
                'success' => true,
                'status' => $status['status'],
                'payment' => [
                    'id' => $payment->payment_id,
                    'status' => $payment->status,
                    'amount' => $payment->formatted_amount,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** ===================== Policy Helpers (Ported from PublicCheckoutController) ===================== */

    /**
     * Devuelve el primer registro de traducción por locale con fallback.
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

    private function canonicalKeyFromSlug(?string $slug): ?string
    {
        if (!$slug) return null;

        $s = \Illuminate\Support\Str::of($slug)
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
                if ($s === $alt || \Illuminate\Support\Str::contains($s, $alt)) {
                    return $key;
                }
            }
        }

        return \Illuminate\Support\Str::slug($s);
    }

    private function buildPolicyBlocksFromDB(string $locale, string $fallback): array
    {
        // Traer políticas activas con sus secciones activas + traducciones
        $policies = \App\Models\Policy::query()
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

            // 1) Contenido a nivel de Policy
            $policyTitle   = trim((string) ($pTr->name ?? ''));
            $policyContent = (string) ($pTr->content ?? '');

            if (trim(strip_tags($policyContent)) !== '') {
                $htmlParts[] = nl2br(e($policyContent));
            }

            // 2) Contenido por secciones activas
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
                if ($sBody !== '') {
                    $htmlParts[] = nl2br(e($sBody));
                }
            }

            // Guardar en el array de bloques
            if (!empty($htmlParts)) {
                // Generar key si no existe
                $key = $this->canonicalKeyFromSlug($p->slug);
                if (!$key) {
                    $key = 'policy_' . $p->policy_id;
                }

                // Título del bloque
                $blockTitle = $policyTitle;
                if ($blockTitle === '') {
                    $blockTitle = (string) ($p->slug ?? '');
                }
                if ($blockTitle === '') {
                    $blockTitle = 'Policy #' . $p->policy_id;
                }

                // Versión
                $version = null;
                if (!empty($p->effective_from) || !empty($p->effective_to)) {
                    $from = $p->effective_from ? \Carbon\Carbon::parse($p->effective_from)->format('Y-m-d') : '—';
                    $to   = $p->effective_to   ? \Carbon\Carbon::parse($p->effective_to)->format('Y-m-d') : '—';
                    $version = "v {$from} → {$to}";
                }

                $blocks[] = [
                    'key'     => $key,
                    'title'   => $blockTitle,
                    'version' => $version ?: 'v1',
                    'html'    => implode("", $htmlParts),
                ];

                // Guardar versión en array de versiones
                if ($key === 'terms'   && !$versions['terms']) {
                    $versions['terms'] = $version ?: 'v1';
                }
                if ($key === 'privacy' && !$versions['privacy']) {
                    $versions['privacy'] = $version ?: 'v1';
                }
            }
        }

        // 5) Orden lógico
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
     * Get enabled gateways list
     */
    private function getEnabledGateways()
    {
        $enabledGateways = [];

        // Helper to check setting
        $isEnabled = function ($key, $default = false) {
            $setting = Setting::where('key', $key)->first();
            $val = $setting?->value ?? $default;
            return filter_var($val, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool) $val;
        };

        // Stripe (default true)
        if ($isEnabled('payment.gateway.stripe', true)) {
            $enabledGateways[] = [
                'id' => 'stripe',
                'name' => 'Stripe',
                'icon' => 'fab fa-cc-stripe',
                'description' => __('payment.stripe_description'),
                // 'logo' removed to use icon
            ];
        }

        // PayPal
        if ($isEnabled('payment.gateway.paypal', false)) {
            $enabledGateways[] = [
                'id' => 'paypal',
                'name' => 'PayPal',
                'icon' => 'fab fa-paypal',
                'description' => __('payment.paypal_description'),
                // 'logo' removed to use icon
            ];
        }

        // Alignet
        if ($isEnabled('payment.gateway.alignet', false)) {
            $enabledGateways[] = [
                'id' => 'alignet',
                'name' => 'Banco Nacional',
                'icon' => 'fas fa-credit-card',
                'description' => __('payment.alignet_description'),
                'logo' => asset('images/bn-logo.png'),
            ];
        }

        // Sort gateways to put default first
        $defaultGateway = config('payment.default_gateway');

        usort($enabledGateways, function ($a, $b) use ($defaultGateway) {
            if ($a['id'] === $defaultGateway) return -1;
            if ($b['id'] === $defaultGateway) return 1;
            return 0;
        });

        return $enabledGateways;
    }

    /**
     * Show Alignet payment form
     */
    /**
     * Show Alignet payment form
     */
    public function showAlignetPaymentForm(Payment $payment)
    {
        // Check if Alignet is enabled
        $gatewayManager = app(\App\Services\PaymentGateway\PaymentGatewayManager::class);
        if (
            !$gatewayManager->isGatewayEnabled('alignet') &&
            !(\App\Models\Setting::where('key', 'payment.gateway.alignet')->value('value') ?? false)
        ) {
            abort(404);
        }

        // Load booking if not already loaded
        // Load booking if not already loaded
        if (!$payment->relationLoaded('booking')) {
            $payment->load('booking.detail', 'booking.tour', 'booking.user');
        }

        $booking = $payment->booking;

        // Check permissions
        if ($booking) {
            if (Auth::check() && Auth::id() !== $booking->user_id && !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized to pay for this booking');
            }
        } else {
            // Cart payment (no booking yet)
            // If payment has user_id, check ownership
            if ($payment->user_id && Auth::check() && Auth::id() !== $payment->user_id && !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized to pay for this payment');
            }
        }

        // Check if already paid
        if ($payment->status === 'completed') {
            return redirect()->route(app()->getLocale() . '.home')
                ->with('info', 'This payment has already been completed');
        }

        // Retrieve stored payment data (generated and signed during initiate)
        $gatewayResponse = $payment->gateway_response ?? [];
        $paymentData = $gatewayResponse['raw'] ?? $gatewayResponse['metadata']['payment_data'] ?? null;

        if (!$paymentData) {
            // Fallback: Try to regenerate IF we have a booking (Old logic fallback)
            // But if we don't have a booking, we fail.
            if (!$booking) {
                Log::error('Alignet payment data missing in gateway_response and no booking to regenerate', ['payment_id' => $payment->payment_id]);
                abort(500, 'Payment data missing');
            }

            // ... regenerate logic could go here, but let's assume valid flow has it ...
            // For now, abort to force correct flow usage
            abort(500, 'Payment data missing/invalid');
        }

        // 🔧 FIX: Ensure urlResponse and timeoutResponse exist (for legacy payments)
        if (!isset($paymentData['urlResponse'])) {
            $paymentData['urlResponse'] = route('webhooks.payment.alignet');
        }
        if (!isset($paymentData['timeoutResponse'])) {
            $paymentData['timeoutResponse'] = '300';
        }

        return view('payments.alignet-form', [
            'booking' => $booking,
            'paymentData' => $paymentData,
            'paymentId' => $payment->payment_id
        ]);
    }

    /**
     * Handle Alignet response from VPOS2
     */
    public function handleAlignetResponse(Request $request)
    {
        Log::info('Alignet Response Received', $request->all());

        // Get authorization result first
        $authResult = $request->input('authorizationResult');
        $operationNumber = $request->input('purchaseOperationNumber');
        $amount = $request->input('purchaseAmount') / 100;
        $bookingId = $request->input('reserved1'); // Booking ID
        $paymentId = $request->input('reserved2'); // 🔥 Payment ID (critical for session-independent recovery)

        // Only validate hash for successful transactions
        // Alignet doesn't send purchaseVerification for cancelled/rejected transactions
        if ($authResult === '00') {
            $alignetService = app(\App\Services\AlignetPaymentService::class);

            if (!$alignetService->validateResponse($request->all())) {
                Log::error('Alignet: Invalid response signature', $request->all());
                return redirect()->route('payment.error')
                    ->with('error', __('m_checkout.payment.invalid_response'));
            }
        } else {
            // Log non-successful transactions without hash validation
            Log::warning('Alignet: Transaction not successful', [
                'authResult' => $authResult,
                'errorCode' => $request->input('errorCode'),
                'errorMessage' => $request->input('errorMessage'),
                'operationNumber' => $operationNumber,
            ]);
        }

        // 🔥 CRITICAL: Find payment by ID (no Auth dependency)
        $payment = null;

        // Priority 1: Search by payment_id from reserved2 (most reliable)
        if ($paymentId) {
            $payment = Payment::find($paymentId);
            if ($payment) {
                Log::info('Alignet: Found payment by ID', [
                    'payment_id' => $paymentId,
                    'booking_id' => $payment->booking_id,
                ]);
            }
        }

        // Priority 2: Search by operation number (fallback)
        if (!$payment) {
            $payment = Payment::where('gateway', 'alignet')
                ->where('gateway_payment_intent_id', $operationNumber)
                ->first();

            if ($payment) {
                Log::info('Alignet: Found payment by operation number', [
                    'operation_number' => $operationNumber,
                    'payment_id' => $payment->payment_id,
                ]);
            }
        }

        // Priority 3: Create new payment (last resort)
        if (!$payment) {
            Log::warning('Alignet: Creating new payment record', [
                'operation_number' => $operationNumber,
                'booking_id' => $bookingId,
                'payment_id_from_reserved2' => $paymentId,
            ]);

            // Get user_id from booking if it exists
            $userId = null;
            if ($bookingId) {
                $booking = \App\Models\Booking::find($bookingId);
                $userId = $booking?->user_id;
            }

            $payment = Payment::create([
                'gateway' => 'alignet',
                'gateway_payment_intent_id' => $operationNumber,
                'booking_id' => $bookingId,
                'user_id' => $userId, // 🔥 From booking, not Auth::id()
                'amount' => $amount,
                'currency' => 'USD',
                'status' => 'pending',
            ]);
        }

        // Actualizar datos específicos de tarjeta de Alignet antes de procesar
        $payment->update([
            'card_bin' => $request->input('bin'),
            'card_brand' => $request->input('brand'),
            'payment_method_type' => 'card',
            'card_last4' => substr($request->input('bin') ?? '', -4),
            'gateway_response' => $request->all(),
            'error_code' => $request->input('errorCode'),
            'error_message' => $request->input('errorMessage'),
        ]);

        if ($authResult === '00') {
            Log::info('Processing successful payment', [
                'payment_id' => $payment->payment_id,
                'auth_result' => $authResult,
            ]);

            try {
                // Delegar al servicio centralizado para manejar creación de bookings, 
                // limpieza de sesión (incluyendo promo codes) y correos.
                $success = $this->paymentService->handleSuccessfulPayment($payment, $request->all());

                Log::info('handleSuccessfulPayment result', [
                    'success' => $success,
                    'payment_id' => $payment->payment_id,
                ]);

                if ($success) {
                    session()->forget('guest_payment_id');

                    // Redirección inteligente
                    $payment->refresh();
                    $successMessage = __('m_checkout.payment.success');

                    // Determine redirect URL
                    if ($payment->booking_id) {
                        $booking = $payment->booking;
                        // Use booking_id to match default route model binding
                        $param = $booking->booking_id;
                        $redirectUrl = route('booking.confirmation', $param);
                    } else {
                        $redirectUrl = route(app()->getLocale() . '.home');
                    }

                    // 🔥 CRITICAL: Generate token to restore session after successful payment
                    $user = $payment->user;

                    if ($user) {
                        $token = \Illuminate\Support\Str::random(64);

                        \Illuminate\Support\Facades\Cache::put(
                            "payment_return_token:{$token}",
                            [
                                'user_id' => $user->user_id,
                                'payment_id' => $payment->payment_id,
                                'redirect_url' => $redirectUrl,
                                'success_message' => $successMessage,
                            ],
                            now()->addMinutes(5)
                        );

                        Log::info('Generated payment return token for successful payment', [
                            'user_id' => $user->user_id,
                            'payment_id' => $payment->payment_id,
                        ]);

                        return redirect()->route('payment.return.restore', ['token' => $token]);
                    }

                    // Guest user - direct redirect
                    return redirect($redirectUrl)->with('success', $successMessage);
                } else {
                    Log::warning('handleSuccessfulPayment returned false', [
                        'payment_id' => $payment->payment_id,
                        'auth_result' => $authResult,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Alignet handling failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'payment_id' => $payment->payment_id,
                ]);
                // Fallthrough to error page
            }
        }

        // Payment failed
        $this->paymentService->handleFailedPayment($payment, $request->input('errorCode'), $request->input('errorMessage'));

        // Preserve cart on payment failure - refresh expiration
        $cartId = $payment->metadata['cart_snapshot']['cart_id'] ?? session('cart_id');
        if ($cartId) {
            try {
                $cart = \App\Models\Cart::find($cartId);
                if ($cart && $cart->is_active) {
                    // Refresh cart expiration to give user more time
                    $cart->refreshExpiry();
                    Log::info('Cart expiration refreshed after payment failure', ['cart_id' => $cartId]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to refresh cart expiration', ['error' => $e->getMessage()]);
            }
        }

        // Translate technical error codes to user-friendly messages
        $errorCode = $request->input('errorCode');
        $errorMessage = $request->input('errorMessage');
        $authResult = $request->input('authorizationResult');

        // Map Alignet error codes to friendly messages
        $friendlyMessage = match ($errorCode) {
            '2300' => __('m_checkout.payment.cancelled_by_user'),  // User cancelled
            '2301' => __('m_checkout.payment.timeout'),            // Timeout
            '2302' => __('m_checkout.payment.insufficient_funds'), // Insufficient funds
            '2303' => __('m_checkout.payment.card_declined'),      // Card declined
            '2304' => __('m_checkout.payment.invalid_card'),       // Invalid card
            default => __('m_checkout.payment.failed')
        };

        // Add technical debug info for bank verification (always show for payment errors)
        $debugInfo = __('m_checkout.payment.debug_info', [
            'code' => $errorCode,
            'auth' => $authResult,
            'message' => $errorMessage,
        ]);

        // Combine friendly message with debug info
        $fullMessage = "{$friendlyMessage} {$debugInfo}";

        Log::info('Alignet payment failed - User-friendly message', [
            'error_code' => $errorCode,
            'auth_result' => $authResult,
            'technical_message' => $errorMessage,
            'friendly_message' => $friendlyMessage,
            'full_message' => $fullMessage,
        ]);

        // 🔥 CRITICAL: Generate temporary token to restore session after cross-site redirect
        $user = $payment->user;

        if ($user) {
            // Create a one-time token valid for 5 minutes
            $token = \Illuminate\Support\Str::random(64);

            \Illuminate\Support\Facades\Cache::put(
                "payment_return_token:{$token}",
                [
                    'user_id' => $user->user_id,
                    'payment_id' => $payment->payment_id,
                    'cart_id' => $cartId,
                    'error_message' => $fullMessage,
                ],
                now()->addMinutes(5)
            );

            Log::info('Generated payment return token for session restoration', [
                'user_id' => $user->user_id,
                'payment_id' => $payment->payment_id,
                'token_preview' => substr($token, 0, 10) . '...',
            ]);

            // Redirect to session restoration endpoint with token
            return redirect()->route('payment.return.restore', ['token' => $token]);
        }

        // If no user (guest), simply redirect to cart
        return redirect()->route('public.carts.index')
            ->with('error', $fullMessage);
    }

    /**
     * Query Alignet transaction (admin only)
     */
    public function queryAlignetTransaction($operationNumber)
    {
        // Only allow admins to query transactions
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $alignetService = app(\App\Services\AlignetPaymentService::class);
        $result = $alignetService->queryTransaction($operationNumber);

        return response()->json([
            'success' => $result !== null,
            'data' => $result
        ]);
    }

    /**
     * Restore user session after payment gateway redirect
     * This solves the session loss issue when returning from Alignet cross-site POST
     */
    public function restoreSession(string $token)
    {
        $data = \Illuminate\Support\Facades\Cache::get("payment_return_token:{$token}");

        if (!$data) {
            Log::warning('Invalid or expired payment return token', [
                'token_preview' => substr($token, 0, 10) . '...'
            ]);

            return redirect()->route('public.carts.index')
                ->with('error', __('m_checkout.payment.session_expired'));
        }

        // Delete token (one-time use)
        \Illuminate\Support\Facades\Cache::forget("payment_return_token:{$token}");

        // Re-authenticate user
        $user = \App\Models\User::find($data['user_id']);

        if ($user) {
            // Login without triggering events (to avoid notifications)
            \Illuminate\Support\Facades\Auth::login($user, true);

            Log::info('User session restored after payment redirect', [
                'user_id' => $user->user_id,
                'payment_id' => $data['payment_id'] ?? null,
            ]);

            // Determine redirect destination
            if (isset($data['redirect_url'])) {
                // Successful payment - redirect to booking confirmation
                return redirect($data['redirect_url'])
                    ->with('success', $data['success_message'] ?? __('m_checkout.payment.success'));
            } else {
                // Failed payment - redirect to cart with error
                return redirect()->route('public.carts.index')
                    ->with('error', $data['error_message'] ?? __('m_checkout.payment.failed'));
            }
        }

        // If user not found, redirect to cart
        Log::warning('User not found for payment return token', [
            'user_id' => $data['user_id'] ?? null,
        ]);

        return redirect()->route('public.carts.index');
    }

    /**
     * Check payment status for manual polling (Alignet Modal workaround)
     */
    public function checkStatus(Payment $payment)
    {
        // 🔒 Security: Only allow checking own payments or guest session payments
        $user = Auth::user();
        $isOwner = $user && $payment->user_id === $user->user_id; // Check user_id key
        $isGuestOwner = session('guest_payment_id') == $payment->payment_id;

        if (!$isOwner && !$isGuestOwner && !$user?->hasRole('admin')) {
            return response()->json(['status' => 'unauthorized'], 403);
        }

        if ($payment->status === 'completed') {
            // Find the associated booking to generate redirect URL
            $booking = Booking::find($payment->booking_id);
            // Verify booking exists before generating route
            if ($booking) {
                $redirectUrl = route('booking.confirmation', $booking->booking_id);
                return response()->json([
                    'status' => 'paid',
                    'redirect_url' => $redirectUrl
                ]);
            }
        }

        return response()->json(['status' => $payment->status]);
    }
}
