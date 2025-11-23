<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use App\Services\PaymentGateway\PaymentGatewayManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    public function __construct(
        protected PaymentGatewayManager $gatewayManager
    ) {}

    /**
     * Process payment for a booking
     *
     * @param Booking $booking
     * @param array $paymentData
     * @return array
     * @throws \Exception
     */
    public function processBookingPayment(Booking $booking, array $paymentData): array
    {
        // Validate booking
        if ($booking->isPaid()) {
            throw new \Exception('Booking is already paid');
        }

        // Get gateway
        $gatewayName = $paymentData['gateway'] ?? config('payment.default_gateway');
        $gateway = $this->gatewayManager->driver($gatewayName);

        // Prepare payment data
        $amount = $paymentData['amount'] ?? $booking->remaining_balance;
        $currency = $paymentData['currency'] ?? config('payment.default_currency', 'USD');

        // 🔄 NUEVO: Buscar pago pendiente existente para reutilizarlo
        $payment = Payment::where('booking_id', $booking->booking_id)
            ->where('user_id', $booking->user_id)
            ->whereIn('status', ['pending', 'processing'])
            ->where('amount', $amount)
            ->where('currency', $currency)
            ->where('gateway', $gatewayName)
            ->first();

        // Si ya existe un pago pendiente con payment_intent, reutilizarlo
        if ($payment && $payment->gateway_payment_intent_id) {
            Log::info('Reusing existing payment record', [
                'payment_id' => $payment->payment_id,
                'booking_id' => $booking->booking_id,
                'gateway_payment_intent_id' => $payment->gateway_payment_intent_id,
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'client_secret' => $payment->gateway_response['client_secret'] ?? null,
                'payment_intent_id' => $payment->gateway_payment_intent_id,
            ];
        }

        // Si existe pago pendiente pero sin intent (falló antes), eliminarlo
        if ($payment && !$payment->gateway_payment_intent_id) {
            Log::info('Deleting incomplete payment record', [
                'payment_id' => $payment->payment_id,
                'booking_id' => $booking->booking_id,
            ]);
            $payment->delete();
            $payment = null;
        }

        // Crear nuevo registro de pago solo si no existe uno válido
        if (!$payment) {
            $payment = Payment::create([
                'booking_id' => $booking->booking_id,
                'user_id' => $booking->user_id,
                'gateway' => $gatewayName,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'metadata' => [
                    'booking_reference' => $booking->booking_reference,
                    'tour_id' => $booking->tour_id,
                    'created_from' => 'checkout',
                ],
            ]);

            Log::info('Created new payment record', [
                'payment_id' => $payment->payment_id,
                'booking_id' => $booking->booking_id,
            ]);
        }

        try {
            // Create payment intent with gateway
            $intentData = [
                'amount' => $amount,
                'currency' => $currency,
                'booking_id' => $booking->booking_id,
                'booking_reference' => $booking->booking_reference,
                'user_id' => $booking->user_id,
                'user_email' => $booking->user->email ?? null,
                'tour_name' => $booking->tour->getTranslatedName() ?? 'Tour',
                'tour_date' => $booking->detail?->tour_date ? $booking->detail->tour_date->format('Y-m-d') : null,
                'description' => "Booking #{$booking->booking_reference}",
                'receipt_email' => $booking->user->email ?? null,
            ];

            // Add customer ID if exists
            if (!empty($paymentData['customer_id'])) {
                $intentData['customer_id'] = $paymentData['customer_id'];
            }

            $result = $gateway->createPaymentIntent($intentData);

            // Update payment with intent details
            $payment->update([
                'gateway_payment_intent_id' => $result['payment_intent_id'],
                'status' => 'processing',
                'gateway_response' => $result,
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'client_secret' => $result['client_secret'] ?? null,
                'payment_intent_id' => $result['payment_intent_id'],
            ];
        } catch (\Exception $e) {
            // Mark payment as failed
            $payment->markAsFailed('intent_creation_failed', $e->getMessage());

            Log::error('Payment intent creation failed', [
                'booking_id' => $booking->booking_id,
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle successful payment
     *
     * @param Payment $payment
     * @param array $gatewayResponse
     * @return bool
     */
    public function handleSuccessfulPayment(Payment $payment, array $gatewayResponse = []): bool
    {
        return DB::transaction(function () use ($payment, $gatewayResponse) {
            // 🔒 IDEMPOTENCY CHECK - Skip if bookings already created
            if ($payment->bookings_created) {
                Log::info('Bookings already created for this payment, skipping duplicate creation', [
                    'payment_id' => $payment->payment_id,
                    'bookings_created_at' => $payment->bookings_created_at,
                    'booking_id' => $payment->booking_id,
                ]);
                return true;
            }

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'gateway_transaction_id' => $gatewayResponse['transaction_id'] ?? $payment->gateway_payment_intent_id,
                'payment_method_type' => $gatewayResponse['payment_method']['type'] ?? null,
                'card_brand' => $gatewayResponse['payment_method']['card_brand'] ?? null,
                'card_last4' => $gatewayResponse['payment_method']['card_last4'] ?? null,
                'gateway_response' => array_merge($payment->gateway_response ?? [], $gatewayResponse),
            ]);

            // Determine booking status based on configuration
            $requireAdminConfirmation = config('booking.require_admin_confirmation', true);
            $bookingStatus = $requireAdminConfirmation ? 'pending' : 'confirmed';

            Log::info('Processing payment with booking status', [
                'payment_id' => $payment->payment_id,
                'booking_status' => $bookingStatus,
                'require_admin_confirmation' => $requireAdminConfirmation,
            ]);

            // 🔄 Create bookings from cart snapshot AFTER successful payment
            $cartSnapshot = $payment->metadata['cart_snapshot'] ?? session('cart_snapshot');
            $createdBookings = collect();

            if ($cartSnapshot && !empty($cartSnapshot['items'])) {
                $createdBookings = $this->createBookingsFromSnapshot($cartSnapshot, $payment, $bookingStatus);

                if ($createdBookings->isEmpty()) {
                    Log::error('Failed to create bookings from snapshot', [
                        'payment_id' => $payment->payment_id,
                        'cart_snapshot_items' => count($cartSnapshot['items'] ?? []),
                    ]);
                    return false;
                }

                // Link first booking to payment
                $payment->update([
                    'booking_id' => $createdBookings->first()->booking_id,
                    'bookings_created' => true,
                    'bookings_created_at' => now(),
                ]);

                Log::info('Bookings created successfully after payment', [
                    'payment_id' => $payment->payment_id,
                    'booking_count' => $createdBookings->count(),
                    'booking_ids' => $createdBookings->pluck('booking_id')->toArray(),
                    'status' => $bookingStatus,
                ]);

                // Clean up cart and session
                $this->cleanupAfterPayment($cartSnapshot);

                // Send confirmation emails
                if (config('booking.send_confirmation_email', true)) {
                    foreach ($createdBookings as $booking) {
                        try {
                            $this->sendConfirmationEmail($booking, $payment);
                        } catch (\Exception $e) {
                            Log::error('Failed to send confirmation email', [
                                'booking_id' => $booking->booking_id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            } else {
                // Backwards compatibility: Handle existing booking (if payment was created with booking_id)
                $booking = $payment->booking;

                if ($booking) {
                    if (!$requireAdminConfirmation) {
                        $booking->update(['status' => 'confirmed']);
                        Log::info('Existing booking auto-confirmed after payment', [
                            'booking_id' => $booking->booking_id,
                            'payment_id' => $payment->payment_id,
                        ]);
                    } else {
                        Log::info('Existing booking payment completed, awaiting admin confirmation', [
                            'booking_id' => $booking->booking_id,
                            'payment_id' => $payment->payment_id,
                            'status' => 'pending',
                        ]);
                    }

                    // Mark as created to prevent re-processing
                    $payment->update([
                        'bookings_created' => true,
                        'bookings_created_at' => now(),
                    ]);

                    // Send confirmation email
                    if (config('booking.send_confirmation_email', true)) {
                        try {
                            $this->sendConfirmationEmail($booking, $payment);
                        } catch (\Exception $e) {
                            Log::error('Failed to send confirmation email', [
                                'booking_id' => $booking->booking_id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                } else {
                    Log::warning('No cart snapshot and no existing booking found for payment', [
                        'payment_id' => $payment->payment_id,
                    ]);
                }
            }

            return true;
        });
    }

    /**
     * Create bookings from cart snapshot
     */
    private function createBookingsFromSnapshot(array $cartSnapshot, Payment $payment, string $status = 'pending')
    {
        $bookingCreator = app(\App\Services\Bookings\BookingCreator::class);
        $bookings = collect();
        $promoApplied = false;

        // Get promo code if exists
        $promoCode = null;
        if (!empty($cartSnapshot['promo_code_id'])) {
            $promoCode = \App\Models\PromoCode::find($cartSnapshot['promo_code_id']);
        }

        foreach ($cartSnapshot['items'] as $item) {
            // Convert categories array to quantity map
            $quantities = [];
            foreach ((array) ($item['categories'] ?? []) as $cat) {
                $qid = (int)($cat['category_id'] ?? 0);
                $qq  = (int)($cat['quantity'] ?? 0);
                if ($qid > 0 && $qq > 0) {
                    $quantities[$qid] = $qq;
                }
            }

            if (empty($quantities)) {
                continue;
            }

            $payload = [
                'user_id'           => $cartSnapshot['user_id'],
                'tour_id'           => $item['tour_id'],
                'schedule_id'       => $item['schedule_id'],
                'tour_language_id'  => $item['tour_language_id'],
                'tour_date'         => $item['tour_date'],
                'booking_date'      => now(),
                'categories'        => $quantities,
                'hotel_id'          => $item['hotel_id'],
                'is_other_hotel'    => (bool) ($item['is_other_hotel'] ?? false),
                'other_hotel_name'  => $item['other_hotel_name'] ?? null,
                'status'            => $status, // Use passed status instead of hardcoded
                'meeting_point_id'  => $item['meeting_point_id'] ?? null,
                'notes'             => $cartSnapshot['notes'] ?? null,
                // Only apply promo code to first booking
                'promo_code'        => $promoApplied ? null : ($promoCode?->code),
                'exclude_cart_id'   => (int) ($cartSnapshot['cart_id'] ?? 0),
            ];

            // BookingCreator handles promo code redemption internally
            $booking = $bookingCreator->create($payload, validateCapacity: false, countHolds: false);

            // Mark promo as applied after first booking
            if (!$promoApplied && $promoCode) {
                $promoApplied = true;
            }

            $bookings->push($booking);
        }

        return $bookings;
    }

    /**
     * Clean up cart and session after successful payment
     */
    private function cleanupAfterPayment(array $cartSnapshot): void
    {
        $userId = $cartSnapshot['user_id'] ?? null;

        if (!$userId) {
            session()->forget(['cart_snapshot', 'cart_reservation_token', 'payment_start_time']);
            return;
        }

        // Find active cart
        $cart = \App\Models\Cart::where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if ($cart) {
            // Delete all items
            $cart->items()->delete();

            // Deactivate cart
            $cart->update([
                'is_active' => false,
                'expires_at' => now()
            ]);

            Log::info('Cart cleared after successful payment', [
                'cart_id' => $cart->cart_id,
                'user_id' => $userId,
            ]);
        }

        // Clear session data
        session()->forget(['cart_snapshot', 'cart_reservation_token', 'payment_start_time']);
    }

    /**
     * Handle failed payment
     *
     * @param Payment $payment
     * @param string|null $errorCode
     * @param string|null $errorMessage
     * @return bool
     */
    public function handleFailedPayment(Payment $payment, ?string $errorCode = null, ?string $errorMessage = null): bool
    {
        $payment->markAsFailed($errorCode, $errorMessage);

        Log::warning('Payment failed', [
            'payment_id' => $payment->payment_id,
            'booking_id' => $payment->booking_id,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);

        // Optionally notify user
        // TODO: Send payment failed email

        return true;
    }

    /**
     * Process refund
     *
     * @param Payment $payment
     * @param float|null $amount
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function processRefund(Payment $payment, ?float $amount = null, array $data = []): array
    {
        if (!$payment->is_refundable) {
            throw new \Exception('Payment is not refundable');
        }

        // Default to full refund
        $refundAmount = $amount ?? $payment->net_amount;

        if ($refundAmount > $payment->net_amount) {
            throw new \Exception('Refund amount exceeds available amount');
        }

        // Get gateway
        $gateway = $this->gatewayManager->driver($payment->gateway);

        try {
            // Process refund with gateway
            $result = $gateway->refundPayment(
                $payment->gateway_transaction_id ?? $payment->gateway_payment_intent_id,
                $refundAmount,
                $data
            );

            // Record refund in payment
            $payment->recordRefund($refundAmount, $result);

            // Update booking status if fully refunded
            if ($payment->is_fully_refunded) {
                $booking = $payment->booking;
                if ($booking) {
                    $booking->update(['status' => 'cancelled']);
                }
            }

            Log::info('Refund processed', [
                'payment_id' => $payment->payment_id,
                'refund_amount' => $refundAmount,
                'refund_id' => $result['refund_id'] ?? null,
            ]);

            return [
                'success' => true,
                'refund_id' => $result['refund_id'] ?? null,
                'amount' => $refundAmount,
                'payment' => $payment->fresh(),
            ];
        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'payment_id' => $payment->payment_id,
                'amount' => $refundAmount,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get payment status from gateway
     *
     * @param Payment $payment
     * @return array
     */
    public function getPaymentStatus(Payment $payment): array
    {
        try {
            $gateway = $this->gatewayManager->driver($payment->gateway);
            return $gateway->getPaymentStatus($payment->gateway_payment_intent_id);
        } catch (\Exception $e) {
            Log::error('Failed to get payment status', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send confirmation email
     *
     * @param Booking $booking
     * @param Payment $payment
     */
    protected function sendConfirmationEmail(Booking $booking, Payment $payment): void
    {
        // Load relationships
        $booking->load(['detail', 'user', 'tour']);

        $details = collect([$booking->detail]);
        $notify = $this->getNotifyEmails();
        $userEmail = $booking->user->email ?? null;

        if (!$userEmail) {
            return;
        }

        $mailable = (new \App\Mail\BookingCreatedMail($booking))
            ->onQueue('mail')
            ->afterCommit();

        try {
            $pending = \Mail::to($userEmail);
            if (!empty($notify)) {
                $pending->bcc($notify);
            }
            $pending->queue($mailable);
        } catch (\Exception $e) {
            Log::error('Failed to queue confirmation email', [
                'booking_id' => $booking->booking_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get notification emails
     */
    protected function getNotifyEmails(): array
    {
        return collect([env('BOOKING_NOTIFY'), env('MAIL_NOTIFICATIONS')])
            ->filter()
            ->flatMap(fn($v) => array_map('trim', explode(',', $v)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
