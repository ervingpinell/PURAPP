<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Tour;
use App\Models\PromoCode;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        // 🔄 Usar cart snapshot en lugar de booking IDs
        $cartSnapshot = session('cart_snapshot');

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

        $expiresAt = \Carbon\Carbon::parse($paymentStartTime)->addMinutes($timeoutMinutes);

        // Si ya se venció la sesión de pago
        if (now()->greaterThan($expiresAt)) {
            session()->forget(['cart_snapshot', 'payment_start_time', 'cart_reservation_token']);

            return redirect()->route('public.carts.index')
                ->with('error', __('payment.session_expired'));
        }

        // Total desde el snapshot
        $total    = $this->calculateTotalFromSnapshot($cartSnapshot);
        $currency = config('payment.default_currency', 'USD');
        $defaultGateway = config('payment.default_gateway', 'stripe');

        // ========================
        // Gateways habilitados
        // ========================
        $enabledGateways = [];

        // STRIPE
        $stripeSetting  = \App\Models\Setting::where('key', 'payment.gateway.stripe')->first();
        $stripeEnabled  = $stripeSetting?->value;

        // Si no hay setting en DB, asumimos Stripe habilitado por defecto
        if ($stripeEnabled === null) {
            $stripeEnabled = true;
        }

        // Normalizar a bool por si en DB guardás '0'/'1'/'true'/'false'
        $stripeEnabled = filter_var($stripeEnabled, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool)$stripeEnabled;

        if ($stripeEnabled) {
            $enabledGateways[] = [
                'id'          => 'stripe',
                'name'        => 'Stripe',
                'description' => 'Tarjeta de crédito o débito',
                'icon'        => 'fab fa-cc-stripe',
            ];
        }

        // PAYPAL
        $paypalSetting  = \App\Models\Setting::where('key', 'payment.gateway.paypal')->first();
        $paypalEnabled  = $paypalSetting?->value ?? false;
        $paypalEnabled  = filter_var($paypalEnabled, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool)$paypalEnabled;

        if ($paypalEnabled) {
            $enabledGateways[] = [
                'id'          => 'paypal',
                'name'        => 'PayPal',
                'description' => 'Cuenta PayPal o tarjeta',
                'icon'        => 'fab fa-paypal',
            ];
        }

        // Clave pública de Stripe (solo para el JS de Stripe)
        $stripeKey = config('payment.gateways.stripe.publishable_key');

        // Items para mostrar en el resumen
        $items = collect($cartSnapshot['items'])->map(function ($item) {
            $tour = \App\Models\Tour::find($item['tour_id']);

            return [
                'tour'       => $tour,
                'tour_date'  => $item['tour_date'],
                'categories' => $item['categories'],
            ];
        });

        return view('public.checkout-payment', compact(
            'items',
            'total',
            'currency',
            'defaultGateway',
            'enabledGateways',
            'stripeKey',
            'expiresAt',
            'cartSnapshot'
        ));
    }


    /**
     * Calculate total
     */
    private function calculateTotalFromSnapshot(array $cartSnapshot): float
    {
        $total = 0;

        foreach ($cartSnapshot['items'] as $item) {
            $tour = Tour::find($item['tour_id']);
            if (!$tour) continue;

            foreach ($item['categories'] as $cat) {
                $price = $tour->prices()
                    ->where('category_id', $cat['category_id'])
                    ->where('is_active', true)
                    ->first();

                if ($price) {
                    $total += $price->price * $cat['quantity'];
                }
            }
        }

        if (!empty($cartSnapshot['promo_code_id'])) {
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
        try {
            $cartSnapshot = session('cart_snapshot');

            if (!$cartSnapshot || empty($cartSnapshot['items'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cart data found',
                ], 400);
            }

            // 1) Gateway solicitado o por defecto
            $gateway = $request->input('gateway', config('payment.default_gateway', 'stripe'));

            // 2) Total + moneda
            $total    = $this->calculateTotalFromSnapshot($cartSnapshot);
            $currency = config('payment.default_currency', 'USD');

            // 3) Buscar pago pendiente existente para ESTE carrito + gateway
            $existingPayment = Payment::where('user_id', Auth::id())
                ->whereIn('status', ['pending', 'processing'])
                ->where('gateway', $gateway)
                ->where('amount', $total)
                ->where('currency', $currency)
                ->whereJsonContains('metadata->cart_snapshot->cart_id', $cartSnapshot['cart_id'] ?? null)
                ->first();

            if ($existingPayment && $existingPayment->gateway_payment_intent_id) {
                $existingResponse = $existingPayment->gateway_response ?? [];

                \Log::info('Reusing existing payment intent', [
                    'payment_id'        => $existingPayment->payment_id,
                    'gateway'           => $gateway,
                    'existing_response' => $existingResponse,
                ]);

                // Retornar datos existentes
                return response()->json([
                    'success'        => true,
                    'client_secret'  => $existingResponse['client_secret'] ?? null,
                    'redirect_url'   => $existingResponse['redirect_url'] ?? $existingResponse['approval_url'] ?? null,
                    'approval_url'   => $existingResponse['redirect_url'] ?? $existingResponse['approval_url'] ?? null,
                    'payment_id'     => $existingPayment->payment_id,
                    'reused'         => true,
                ]);
            }

            // 4) Crear nuevo registro de pago
            $payment = Payment::create([
                'user_id'   => Auth::id(),
                'booking_id' => null,
                'gateway'   => $gateway,
                'amount'    => $total,
                'currency'  => $currency,
                'status'    => 'pending',
                'metadata'  => [
                    'cart_snapshot' => $cartSnapshot,
                    'created_from'  => 'checkout',
                ],
            ]);

            // 5) Crear intent/orden en el gateway seleccionado
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

            $intentData = [
                'amount'        => $total,
                'currency'      => $currency,
                'user_id'       => Auth::id(),
                'user_email'    => Auth::user()->email ?? null,
                'description'   => 'Cart checkout',
                'receipt_email' => Auth::user()->email ?? null,
                'options'       => [
                    'return_url' => route('payment.return'),
                    'cancel_url' => route('payment.cancel'),
                ],
            ];

            $result = $gatewayDriver->createPaymentIntent($intentData);

            // 6) Guardar datos del intent usando el DTO
            $payment->update([
                'gateway_payment_intent_id' => $result->paymentIntentId,
                'status'                    => 'processing',
                'gateway_response'          => $result->toArray(),
            ]);

            \Log::info('Gateway intent created successfully', [
                'gateway' => $gateway,
                'intent'  => $result->toArray(),
            ]);

            return response()->json([
                'success'       => true,
                'client_secret' => $result->clientSecret,
                'redirect_url'  => $result->redirectUrl,
                'approval_url'  => $result->redirectUrl, // Alias
                'payment_id'    => $payment->payment_id,
                'reused'        => false,
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment initiation failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
        $request->validate(['payment_intent_id' => 'required']);

        try {
            $payment = Payment::where('gateway_payment_intent_id', $request->payment_intent_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $status = $this->paymentService->getPaymentStatus($payment);

            if ($status['status'] === 'succeeded') {
                $this->paymentService->handleSuccessfulPayment($payment, $status);

                $booking = Booking::find($payment->booking_id);

                session()->forget(['pending_booking_ids']);

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
        if ($payment->user_id !== Auth::id()) {
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
}
