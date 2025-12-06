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
        // 🔄 Usar cart snapshot como fuente de verdad para importe
        $cartSnapshot = session('cart_snapshot');
        $bookingId = $request->input('booking_id');
        $booking = null;

        // Si viene un booking_id (link de pago), intentamos reconstruir el snapshot desde la reserva
        if ($bookingId && !$cartSnapshot) {
            $booking = Booking::with(['detail', 'tour', 'user', 'detail.schedule'])->find($bookingId);

            if ($booking && ($booking->status === 'pending' || $booking->status === 'confirmed')) {
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

                Log::info('Payment Show - Snapshot reconstructed from booking', [
                    'booking_id' => $booking->booking_id,
                    'total' => $booking->total,
                    'subtotal' => $booking->detail?->total,
                    'currency' => $booking->currency
                ]);
            }
        }

        Log::info('Payment Show - Snapshot Received:', [
            'exists'      => (bool) $cartSnapshot,
            'is_array'    => is_array($cartSnapshot),
            'item_count'  => isset($cartSnapshot['items']) ? count($cartSnapshot['items']) : 0,
            'keys'        => $cartSnapshot ? array_keys($cartSnapshot) : [],
            'booking_id'  => $bookingId
        ]);

        if (!$cartSnapshot || empty($cartSnapshot['items'])) {
            return redirect()->route('public.carts.index')
                ->with('error', __('payment.no_cart_data'));
        }

        // Tiempo para completar el pago
        $timeoutMinutes = config('booking.payment_completion_timeout_minutes', 20);

        // Momento de inicio del pago (lo guardamos la primera vez que entra a /payment)
        $paymentStartTime = session('payment_start_time');
        if (!$paymentStartTime) {
            $paymentStartTime = now();
            session(['payment_start_time' => $paymentStartTime]);
        }

        $expiresAt = \Carbon\Carbon::parse($paymentStartTime)
            ->addMinutes($timeoutMinutes);

        // Si ya se venció la sesión de pago
        if (now()->greaterThan($expiresAt)) {
            session()->forget(['cart_snapshot', 'payment_start_time', 'cart_reservation_token']);

            return redirect()->route('public.carts.index')
                ->with('error', __('payment.session_expired'));
        }

        // ========================
        // Total SIEMPRE desde el snapshot
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
            'gateway' => 'required|string|in:stripe,paypal,tilopay,banco_nacional,bac,bcr',
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

            // If we reused an existing payment, return it immediately
            if ($payment->wasRecentlyCreated === false && $payment->gateway_payment_intent_id) {
                $existingResponse = $payment->gateway_response ?? [];

                Log::info('Reusing existing payment intent', [
                    'payment_id' => $payment->payment_id,
                    'gateway' => $gateway,
                    'booking_id' => $bookingId,
                ]);

                // Store payment ID in session for guest status polling
                session(['guest_payment_id' => $payment->payment_id]);

                return response()->json([
                    'success'        => true,
                    'client_secret'  => $existingResponse['client_secret'] ?? null,
                    'redirect_url'   => $existingResponse['redirect_url'] ?? $existingResponse['approval_url'] ?? null,
                    'approval_url'   => $existingResponse['redirect_url'] ?? $existingResponse['approval_url'] ?? null,
                    'payment_id'     => $payment->payment_id,
                    'reused'         => true,
                ]);
            }

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

            // If no user email from auth, try to get from booking
            if (!$userEmail && $bookingId) {
                $booking = \App\Models\Booking::with('user')->find($bookingId);
                $userEmail = $booking?->user?->email;
            }

            $intentData = [
                'amount'        => $total,
                'currency'      => $currency,
                'user_id'       => $userId,
                'user_email'    => $userEmail,
                'description'   => 'Cart checkout',
                'receipt_email' => $userEmail,
                'options'       => [
                    'return_url' => route('payment.return'),
                    'cancel_url' => route('payment.cancel'),
                ],
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

            return response()->json([
                'success'       => true,
                'client_secret' => $result->clientSecret,
                'redirect_url'  => $result->redirectUrl,
                'approval_url'  => $result->redirectUrl, // Alias for backward compatibility
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

        // Try to find an existing active payment intent
        $existingPayment = Payment::where('user_id', $userId)
            ->where('gateway', $gateway)
            ->whereIn('status', ['pending', 'processing'])
            ->where('expires_at', '>', now())
            ->where('amount', $total)
            ->where('currency', $currency)
            ->when($bookingId, function ($query) use ($bookingId) {
                return $query->where('booking_id', $bookingId);
            })
            ->orderByDesc('created_at')
            ->first();

        if ($existingPayment) {
            Log::info('Found existing valid payment intent', [
                'payment_id' => $existingPayment->payment_id,
                'gateway' => $gateway,
                'booking_id' => $bookingId,
                'expires_at' => $existingPayment->expires_at,
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
            ],
        ]);

        Log::info('Created new payment intent', [
            'payment_id' => $payment->payment_id,
            'gateway' => $gateway,
            'booking_id' => $bookingId,
            'expires_at' => $expiresAt,
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

                session()->forget(['pending_booking_ids', 'guest_payment_id']);

                return view('public.payment-confirmation', compact('booking'));
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
                $htmlParts[] = $policyContent;
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
                    $htmlParts[] = $sBody;
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
                'logo' => asset('images/stripe-logo.svg'),
            ];
        }

        // PayPal
        if ($isEnabled('payment.gateway.paypal', false)) {
            $enabledGateways[] = [
                'id' => 'paypal',
                'name' => 'PayPal',
                'icon' => 'fab fa-paypal',
                'description' => __('payment.paypal_description'),
                'logo' => asset('images/paypal-logo.svg'),
            ];
        }

        // Tilopay
        if ($isEnabled('payment.gateway.tilopay', false)) {
            $enabledGateways[] = [
                'id' => 'tilopay',
                'name' => 'Tilopay',
                'icon' => 'fas fa-credit-card',
                'description' => __('payment.tilopay_description'),
                'logo' => asset('images/tilopay-logo.svg'), // Assuming logo exists or use generic
            ];
        }

        // Banco Nacional
        if ($isEnabled('payment.gateway.banco_nacional', false)) {
            $enabledGateways[] = [
                'id' => 'banco_nacional',
                'name' => 'Banco Nacional',
                'icon' => 'fas fa-university',
                'description' => __('payment.banco_nacional_description'),
                'logo' => asset('images/bn-logo.svg'),
            ];
        }

        // BAC
        if ($isEnabled('payment.gateway.bac', false)) {
            $enabledGateways[] = [
                'id' => 'bac',
                'name' => 'BAC Credomatic',
                'icon' => 'fas fa-university',
                'description' => __('payment.bac_description'),
                'logo' => asset('images/bac-logo.svg'),
            ];
        }

        // BCR
        if ($isEnabled('payment.gateway.bcr', false)) {
            $enabledGateways[] = [
                'id' => 'bcr',
                'name' => 'Banco de Costa Rica',
                'icon' => 'fas fa-university',
                'description' => __('payment.bcr_description'),
                'logo' => asset('images/bcr-logo.svg'),
            ];
        }

        return $enabledGateways;
    }
}
