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
            // Update payment
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'gateway_transaction_id' => $gatewayResponse['transaction_id'] ?? $payment->gateway_payment_intent_id,
                'payment_method_type' => $gatewayResponse['payment_method']['type'] ?? null,
                'card_brand' => $gatewayResponse['payment_method']['card_brand'] ?? null,
                'card_last4' => $gatewayResponse['payment_method']['card_last4'] ?? null,
                'gateway_response' => array_merge($payment->gateway_response ?? [], $gatewayResponse),
            ]);

            // Update booking status based on configuration
            $booking = $payment->booking;

            // Check if admin confirmation is required
            $requireAdminConfirmation = config('booking.require_admin_confirmation', true);

            if ($booking && !$requireAdminConfirmation) {
                // Auto-confirm only if admin confirmation is NOT required
                $booking->update(['status' => 'confirmed']);

                Log::info('Booking auto-confirmed after payment', [
                    'booking_id' => $booking->booking_id,
                    'payment_id' => $payment->payment_id,
                ]);
            } elseif ($booking && $requireAdminConfirmation) {
                // Keep as pending for admin review
                Log::info('Booking payment completed, awaiting admin confirmation', [
                    'booking_id' => $booking->booking_id,
                    'payment_id' => $payment->payment_id,
                    'status' => 'pending',
                ]);
            }

            // 🔄 NUEVO: Crear bookings desde cart snapshot DESPUÉS del pago exitoso
            $cartSnapshot = $payment->metadata['cart_snapshot'] ?? session('cart_snapshot');
            $createdBookings = collect();

            if ($cartSnapshot && !empty($cartSnapshot['items'])) {
                $createdBookings = $this->createBookingsFromSnapshot($cartSnapshot, $payment);

                // Link first booking to payment
                if ($createdBookings->isNotEmpty()) {
                    $payment->update(['booking_id' => $createdBookings->first()->booking_id]);
                }

                Log::info('Bookings created after successful payment', [
                    'payment_id' => $payment->payment_id,
                    'booking_count' => $createdBookings->count(),
                    'booking_ids' => $createdBookings->pluck('booking_id')->toArray(),
                ]);
            }

            // 🔄 NUEVO: Limpiar carrito después de crear bookings
            if ($cartSnapshot) {
                $userId = $cartSnapshot['user_id'] ?? null;

                if ($userId) {
                    // Buscar carrito activo del usuario con items reservados
                    $cart = \App\Models\Cart::where('user_id', $userId)
                        ->where('is_active', true)
                        ->whereHas('items', function ($q) {
                            $q->where('is_reserved', true);
                        })
                        ->first();

                    if ($cart) {
                        // Eliminar items reservados
                        $cart->items()->where('is_reserved', true)->delete();

                        // Si no quedan items, desactivar el carrito
                        if ($cart->items()->count() === 0) {
                            $cart->update([
                                'is_active' => false,
                                'expires_at' => now()
                            ]);

                            Log::info('Cart cleared after successful payment', [
                                'cart_id' => $cart->cart_id,
                                'user_id' => $userId,
                            ]);
                        }

                        // Crear nuevo carrito para futuras compras con timer fresco
                        $newCart = \App\Models\Cart::create([
                            'user_id' => $userId,
                            'is_active' => true,
                            'expires_at' => now()->addMinutes(config('cart.expiration_minutes', 30)),
                        ]);

                        Log::info('New cart created with fresh timer', [
                            'cart_id' => $newCart->cart_id,
                            'user_id' => $userId,
                            'expires_at' => $newCart->expires_at,
                        ]);
                    }
                }

                // Clear session data
                session()->forget(['cart_snapshot', 'cart_reservation_token', 'payment_start_time']);
            }

            // Send confirmation emails for all created bookings
            if ($createdBookings->isNotEmpty() && config('booking.send_confirmation_email', true)) {
                foreach ($createdBookings as $createdBooking) {
                    try {
                        $this->sendConfirmationEmail($createdBooking, $payment);
                    } catch (\Exception $e) {
                        Log::error('Failed to send confirmation email', [
                            'booking_id' => $createdBooking->booking_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Send confirmation email for original booking (backwards compatibility)
            if ($booking && config('booking.send_confirmation_email', true)) {
                try {
                    $this->sendConfirmationEmail($booking, $payment);
                } catch (\Exception $e) {
                    Log::error('Failed to send confirmation email', [
                        'booking_id' => $booking->booking_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return true;
        });
    }

    /**
     * Create bookings from cart snapshot
     */
    private function createBookingsFromSnapshot(array $cartSnapshot, Payment $payment)
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
                'status'            => 'confirmed', // Confirmed immediately since payment succeeded
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
