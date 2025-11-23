<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
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
        // 🔄 NUEVO: Usar cart snapshot en lugar de booking IDs
        $cartSnapshot = session('cart_snapshot');

        if (!$cartSnapshot || empty($cartSnapshot['items'])) {
            return redirect()->route('public.carts.index')
                ->with('error', __('payment.no_cart_data'));
        }

        // Calculate payment expiration time
        $timeoutMinutes = config('booking.payment_completion_timeout_minutes', 20);

        // Get payment start time from session, or initialize it now
        $paymentStartTime = session('payment_start_time');
        if (!$paymentStartTime) {
            // Initialize payment start time to NOW (when user first accesses payment page)
            $paymentStartTime = now();
            session(['payment_start_time' => $paymentStartTime]);
        }

        $expiresAt = \Carbon\Carbon::parse($paymentStartTime)->addMinutes($timeoutMinutes);

        // Check if payment session has expired
        if (now()->greaterThan($expiresAt)) {
            // Clear session
            session()->forget(['cart_snapshot', 'payment_start_time', 'cart_reservation_token']);

            return redirect()->route('public.carts.index')
                ->with('error', __('payment.session_expired'));
        }

        // Calculate total from cart snapshot
        $total = $this->calculateTotalFromSnapshot($cartSnapshot);
        $currency = config('payment.default_currency', 'USD');
        $gateway = config('payment.default_gateway', 'stripe');

        // Get Stripe publishable key
        $stripeKey = config('payment.gateways.stripe.publishable_key');

        // Prepare items for display
        $items = collect($cartSnapshot['items'])->map(function ($item) {
            $tour = \App\Models\Tour::find($item['tour_id']);
            return [
                'tour' => $tour,
                'tour_date' => $item['tour_date'],
                'categories' => $item['categories'],
            ];
        });

        return view('public.checkout-payment', compact(
            'items',
            'total',
            'currency',
            'gateway',
            'stripeKey',
            'expiresAt',
            'cartSnapshot'
        ));
    }

    /**
     * Calculate total from cart snapshot
     */
    private function calculateTotalFromSnapshot(array $cartSnapshot): float
    {
        $total = 0;

        foreach ($cartSnapshot['items'] as $item) {
            $tour = \App\Models\Tour::find($item['tour_id']);
            if (!$tour) continue;

            $categories = $item['categories'] ?? [];
            foreach ($categories as $cat) {
                $categoryId = $cat['category_id'] ?? null;
                $quantity = $cat['quantity'] ?? 0;

                if (!$categoryId || !$quantity) continue;

                // Find price for this category
                $price = $tour->prices()
                    ->where('category_id', $categoryId)
                    ->where('is_active', true)
                    ->first();

                if ($price) {
                    $total += $price->price * $quantity;
                }
            }
        }

        // Apply promo code discount if exists
        if (!empty($cartSnapshot['promo_code_id'])) {
            $promoCode = \App\Models\PromoCode::find($cartSnapshot['promo_code_id']);
            if ($promoCode) {
                // Calculate discount based on type
                $discount = 0;

                if ($promoCode->discount_percent > 0) {
                    $discount = $total * ($promoCode->discount_percent / 100);
                } elseif ($promoCode->discount_amount > 0) {
                    $discount = $promoCode->discount_amount;
                }

                // Apply operation: 'subtract' (discount) or 'add' (surcharge)
                if ($promoCode->operation === 'add') {
                    $total += $discount;
                } else {
                    $total -= $discount;
                }
            }
        }

        return max(0, $total);
    }

    /**
     * Initiate payment
     */
    public function initiate(Request $request)
    {
        // 🔄 NUEVO: No validar booking_ids, usar cart snapshot
        try {
            $cartSnapshot = session('cart_snapshot');

            if (!$cartSnapshot || empty($cartSnapshot['items'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cart data found',
                ], 400);
            }

            // Calculate total from snapshot
            $total = $this->calculateTotalFromSnapshot($cartSnapshot);
            $currency = config('payment.default_currency', 'USD');
            $gateway = config('payment.default_gateway', 'stripe');

            // Create payment record (without booking_id - will be set after payment success)
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'booking_id' => null, // Will be set after bookings are created
                'gateway' => $gateway,
                'amount' => $total,
                'currency' => $currency,
                'status' => 'pending',
                'metadata' => [
                    'cart_snapshot' => $cartSnapshot,
                    'created_from' => 'checkout',
                ],
            ]);

            // Create payment intent with gateway
            $gatewayManager = app(\App\Services\PaymentGateway\PaymentGatewayManager::class);
            $gatewayDriver = $gatewayManager->driver($gateway);

            $intentData = [
                'amount' => $total,
                'currency' => $currency,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email ?? null,
                'description' => "Cart checkout",
                'receipt_email' => Auth::user()->email ?? null,
            ];

            $result = $gatewayDriver->createPaymentIntent($intentData);

            // Update payment with intent details
            $payment->update([
                'gateway_payment_intent_id' => $result['payment_intent_id'],
                'status' => 'processing',
                'gateway_response' => $result,
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $result['client_secret'],
                'payment_id' => $payment->payment_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm payment (called after successful payment)
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        try {
            // Find payment by intent ID
            $payment = Payment::where('gateway_payment_intent_id', $request->input('payment_intent_id'))
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Get payment status from gateway
            $status = $this->paymentService->getPaymentStatus($payment);

            if ($status['status'] === 'succeeded') {
                // Handle successful payment
                $this->paymentService->handleSuccessfulPayment($payment, $status);

                // Get booking
                $booking = Booking::find($payment->booking_id);

                // Clear session
                session()->forget('pending_booking_ids');

                return view('public.payment-confirmation', compact('booking'));
            }

            return redirect()->route('payment.show')
                ->with('error', __('Payment was not successful. Please try again.'));
        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('payment.show')
                ->with('error', __('An error occurred while confirming your payment.'));
        }
    }

    /**
     * Cancel payment
     */
    public function cancel(Request $request)
    {
        return redirect()->route('payment.show')
            ->with('warning', __('Payment was cancelled. You can try again when ready.'));
    }

    /**
     * Check payment status (AJAX)
     */
    public function status(Request $request, Payment $payment)
    {
        // Verify ownership
        if ($payment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
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
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
