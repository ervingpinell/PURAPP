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
     * Process payment for a single booking (flujo legacy)
     *
     * @param  Booking  $booking
     * @param  array    $paymentData
     * @return array
     * @throws \Exception
     */
    public function processBookingPayment(Booking $booking, array $paymentData): array
    {
        // Booking ya pagado
        if ($booking->isPaid()) {
            throw new \Exception('Booking is already paid');
        }

        // Gateway actual
        $gatewayName = $paymentData['gateway'] ?? config('payment.default_gateway');
        $gateway     = $this->gatewayManager->driver($gatewayName);

        // Monto y moneda
        $amount   = $paymentData['amount']   ?? $booking->remaining_balance;
        $currency = $paymentData['currency'] ?? config('payment.default_currency', 'USD');

        // 🔄 Buscar pago pendiente existente para reutilizarlo
        $payment = Payment::where('booking_id', $booking->booking_id)
            ->where('user_id', $booking->user_id)
            ->whereIn('status', ['pending', 'processing'])
            ->where('amount', $amount)
            ->where('currency', $currency)
            ->where('gateway', $gatewayName)
            ->first();

        // Si ya existe un pago pendiente con intent, reutilizarlo
        if ($payment && $payment->gateway_payment_intent_id) {
            Log::info('Reusing existing payment record', [
                'payment_id'               => $payment->payment_id,
                'booking_id'               => $booking->booking_id,
                'gateway_payment_intent_id' => $payment->gateway_payment_intent_id,
                'gateway'                  => $gatewayName,
            ]);

            return [
                'success'            => true,
                'payment'            => $payment,
                'client_secret'      => $payment->gateway_response['client_secret'] ?? null,
                'payment_intent_id'  => $payment->gateway_payment_intent_id,
                // para gateways redirect (PayPal, bancos, etc.)
                'redirect_url'       => $payment->gateway_response['redirect_url']
                    ?? $payment->gateway_response['approval_url']
                    ?? null,
            ];
        }

        // Si existe pago pendiente pero sin intent, eliminarlo
        if ($payment && !$payment->gateway_payment_intent_id) {
            Log::info('Deleting incomplete payment record', [
                'payment_id' => $payment->payment_id,
                'booking_id' => $booking->booking_id,
                'gateway'    => $gatewayName,
            ]);

            $payment->delete();
            $payment = null;
        }

        // Crear un nuevo registro de pago si no hay uno válido
        if (!$payment) {
            $payment = Payment::create([
                'booking_id' => $booking->booking_id,
                'user_id'    => $booking->user_id,
                'gateway'    => $gatewayName,
                'amount'     => $amount,
                'currency'   => $currency,
                'status'     => 'pending',
                'metadata'   => [
                    'booking_reference' => $booking->booking_reference,
                    'tour_id'           => $booking->tour_id,
                    'created_from'      => 'checkout',
                ],
            ]);

            Log::info('Created new payment record', [
                'payment_id' => $payment->payment_id,
                'booking_id' => $booking->booking_id,
                'gateway'    => $gatewayName,
            ]);
        }

        try {
            // Crear intent en el gateway
            $intentData = [
                'amount'            => $amount,
                'currency'          => $currency,
                'booking_id'        => $booking->booking_id,
                'booking_reference' => $booking->booking_reference,
                'user_id'           => $booking->user_id,
                'user_email'        => $booking->user->email ?? null,
                'tour_name'         => $booking->tour->getTranslatedName() ?? 'Tour',
                'tour_date'         => $booking->detail?->tour_date
                    ? $booking->detail->tour_date->format('Y-m-d')
                    : null,
                'description'       => "Booking #{$booking->booking_reference}",
                'receipt_email'     => $booking->user->email ?? null,
            ];

            if (!empty($paymentData['customer_id'])) {
                $intentData['customer_id'] = $paymentData['customer_id'];
            }

            $result = $gateway->createPaymentIntent($intentData);

            // Actualizar registro de pago usando el DTO
            $payment->update([
                'gateway_payment_intent_id' => $result->paymentIntentId,
                'status'                    => 'processing',
                'gateway_response'          => $result->toArray(),
            ]);

            Log::info('Payment intent created and stored', [
                'payment_id'          => $payment->payment_id,
                'booking_id'          => $booking->booking_id,
                'gateway'            => $gatewayName,
                'payment_intent_id'  => $result->paymentIntentId,
            ]);

            return [
                'success'           => true,
                'payment'           => $payment,
                'client_secret'     => $result->clientSecret,
                'payment_intent_id' => $result->paymentIntentId,
                'redirect_url'      => $result->redirectUrl,
            ];
        } catch (\Exception $e) {
            // Marcar pago como fallido
            $payment->markAsFailed('intent_creation_failed', $e->getMessage());

            Log::error('Payment intent creation failed', [
                'booking_id' => $booking->booking_id,
                'payment_id' => $payment->payment_id,
                'gateway'    => $gatewayName,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Maneja el flujo posterior a un pago exitoso (cart snapshot o booking directo)
     */
    public function handleSuccessfulPayment(Payment $payment, array $gatewayResponse = []): bool
    {
        return DB::transaction(function () use ($payment, $gatewayResponse) {
            // Idempotencia: si ya se crearon bookings, no hacer nada más
            if ($payment->bookings_created) {
                Log::info('Bookings already created for this payment, skipping duplicate creation', [
                    'payment_id'          => $payment->payment_id,
                    'bookings_created_at' => $payment->bookings_created_at,
                    'booking_id'          => $payment->booking_id,
                ]);
                return true;
            }

            // Actualizar pago
            $payment->update([
                'status'                 => 'completed',
                'paid_at'                => now(),
                'gateway_transaction_id' => $gatewayResponse['transaction_id']
                    ?? $gatewayResponse['id']
                    ?? $payment->gateway_payment_intent_id,
                'payment_method_type'    => $gatewayResponse['payment_method']['type']        ?? null,
                'card_brand'             => $gatewayResponse['payment_method']['card_brand']  ?? null,
                'card_last4'             => $gatewayResponse['payment_method']['card_last4']  ?? null,
                'gateway_response'       => array_merge(
                    is_array($payment->gateway_response ?? null)
                        ? $payment->gateway_response
                        : [],
                    $gatewayResponse
                ),
            ]);

            $requireAdminConfirmation = config('booking.require_admin_confirmation', true);
            $bookingStatus            = $requireAdminConfirmation ? 'pending' : 'confirmed';

            Log::info('Processing payment with booking status', [
                'payment_id'               => $payment->payment_id,
                'booking_status'           => $bookingStatus,
                'require_admin_confirmation' => $requireAdminConfirmation,
            ]);

            // Get cart snapshot from payment metadata or session
            $cartSnapshot = $payment->metadata['cart_snapshot'] ?? session('cart_snapshot');
            $createdBookings = collect();

            // ======== Check if this is a booking payment (existing booking) ========
            $isBookingPayment = !empty($cartSnapshot['is_booking_payment']);
            $existingBookingId = $cartSnapshot['booking_id'] ?? null;

            if ($isBookingPayment && $existingBookingId) {
                // This is a payment for an existing booking (created from admin)
                // Update the existing booking instead of creating a new one
                $booking = Booking::find($existingBookingId);

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
                            'status'     => 'pending',
                        ]);
                    }

                    // Mark as processed
                    $payment->update([
                        'booking_id'          => $booking->booking_id,
                        'bookings_created'    => true,
                        'bookings_created_at' => now(),
                    ]);

                    // Email de confirmación
                    if (config('booking.send_confirmation_email', true)) {
                        try {
                            $this->sendConfirmationEmail($booking, $payment);
                        } catch (\Exception $e) {
                            Log::error('Failed to send confirmation email', [
                                'booking_id' => $booking->booking_id,
                                'error'      => $e->getMessage(),
                            ]);
                        }
                    }

                    // Clean up session
                    session()->forget(['cart_snapshot', 'cart_reservation_token', 'payment_start_time']);

                    return true;
                } else {
                    Log::error('Booking payment but booking not found', [
                        'payment_id' => $payment->payment_id,
                        'booking_id' => $existingBookingId,
                    ]);
                    return false;
                }
            }

            // ======== Flujo normal: crear bookings desde cart_snapshot ========
            if ($cartSnapshot && !empty($cartSnapshot['items'])) {
                $createdBookings = $this->createBookingsFromSnapshot($cartSnapshot, $payment, $bookingStatus);

                if ($createdBookings->isEmpty()) {
                    Log::error('Failed to create bookings from snapshot', [
                        'payment_id'           => $payment->payment_id,
                        'cart_snapshot_items'  => count($cartSnapshot['items'] ?? []),
                    ]);
                    return false;
                }

                // Vincular primera reserva al pago
                $payment->update([
                    'booking_id'          => $createdBookings->first()->booking_id,
                    'bookings_created'    => true,
                    'bookings_created_at' => now(),
                ]);

                // 🔄 Actualizar terms_acceptances con booking_ref
                try {
                    $cartId = $cartSnapshot['cart_id'] ?? null;
                    if ($cartId) {
                        DB::table('terms_acceptances')
                            ->where('cart_ref', $cartId)
                            ->whereNull('booking_ref')
                            ->update([
                                'booking_ref' => $createdBookings->first()->booking_id,
                                'updated_at'  => now(),
                            ]);

                        Log::info('Updated terms_acceptances with booking_ref', [
                            'cart_id'     => $cartId,
                            'booking_id'  => $createdBookings->first()->booking_id,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to update terms_acceptances booking_ref', [
                        'cart_id' => $cartSnapshot['cart_id'] ?? null,
                        'error'   => $e->getMessage(),
                    ]);
                    // No bloqueamos el flujo si falla la actualización
                }

                Log::info('Bookings created successfully after payment', [
                    'payment_id'  => $payment->payment_id,
                    'booking_count' => $createdBookings->count(),
                    'booking_ids' => $createdBookings->pluck('booking_id')->toArray(),
                    'status'      => $bookingStatus,
                ]);

                // Limpiar carrito y sesión
                $this->cleanupAfterPayment($cartSnapshot);

                // Enviar correos
                if (config('booking.send_confirmation_email', true)) {
                    foreach ($createdBookings as $booking) {
                        try {
                            $this->sendConfirmationEmail($booking, $payment);
                        } catch (\Exception $e) {
                            Log::error('Failed to send confirmation email', [
                                'booking_id' => $booking->booking_id,
                                'error'      => $e->getMessage(),
                            ]);
                        }
                    }
                }
            } else {
                // ======== Flujo legacy: payment ya tenía booking_id ========
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
                            'status'     => 'pending',
                        ]);
                    }

                    // Evitar reprocesar
                    $payment->update([
                        'bookings_created'    => true,
                        'bookings_created_at' => now(),
                    ]);

                    // Email de confirmación
                    if (config('booking.send_confirmation_email', true)) {
                        try {
                            $this->sendConfirmationEmail($booking, $payment);
                        } catch (\Exception $e) {
                            Log::error('Failed to send confirmation email', [
                                'booking_id' => $booking->booking_id,
                                'error'      => $e->getMessage(),
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
     * Crear bookings desde el snapshot del carrito
     */
    private function createBookingsFromSnapshot(array $cartSnapshot, Payment $payment, string $status = 'pending')
    {
        $bookingCreator = app(\App\Services\Bookings\BookingCreator::class);
        $bookings       = collect();
        $promoApplied   = false;

        // Promo, si existe
        $promoCode = null;
        if (!empty($cartSnapshot['promo_code_id'])) {
            $promoCode = \App\Models\PromoCode::find($cartSnapshot['promo_code_id']);
        }

        foreach ($cartSnapshot['items'] as $item) {
            // categories -> [category_id => quantity]
            $quantities = [];
            foreach ((array) ($item['categories'] ?? []) as $cat) {
                $qid = (int) ($cat['category_id'] ?? 0);
                $qq  = (int) ($cat['quantity']    ?? 0);
                if ($qid > 0 && $qq > 0) {
                    $quantities[$qid] = $qq;
                }
            }

            if (empty($quantities)) {
                continue;
            }

            $payload = [
                'user_id'          => $cartSnapshot['user_id'],
                'tour_id'          => $item['tour_id'],
                'schedule_id'      => $item['schedule_id'],
                // Handle both 'tour_language_id' (cart) and 'language_id' (booking payment)
                'tour_language_id' => $item['tour_language_id'] ?? $item['language_id'] ?? null,
                'tour_date'        => $item['tour_date'],
                'booking_date'     => now(),
                'categories'       => $quantities,
                'hotel_id'         => $item['hotel_id'] ?? null,
                'is_other_hotel'   => (bool) ($item['is_other_hotel'] ?? false),
                'other_hotel_name' => $item['other_hotel_name'] ?? null,
                'status'           => $status,
                'meeting_point_id' => $item['meeting_point_id'] ?? null,
                'notes'            => $cartSnapshot['notes'] ?? null,
                // sólo primer booking con promo
                'promo_code'       => $promoApplied ? null : ($promoCode?->code),
                'exclude_cart_id'  => (int) ($cartSnapshot['cart_id'] ?? 0),
            ];

            $booking = $bookingCreator->create($payload, validateCapacity: false, countHolds: false);

            if (!$promoApplied && $promoCode) {
                $promoApplied = true;
            }

            $bookings->push($booking);
        }

        return $bookings;
    }

    /**
     * Limpiar carrito y sesión después de pago exitoso
     */
    private function cleanupAfterPayment(array $cartSnapshot): void
    {
        $userId = $cartSnapshot['user_id'] ?? null;

        if (!$userId) {
            session()->forget(['cart_snapshot', 'cart_reservation_token', 'payment_start_time']);
            return;
        }

        $cart = \App\Models\Cart::where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if ($cart) {
            $cart->items()->delete();

            $cart->update([
                'is_active'  => false,
                'expires_at' => now(),
            ]);

            Log::info('Cart cleared after successful payment', [
                'cart_id' => $cart->cart_id,
                'user_id' => $userId,
            ]);
        }

        session()->forget(['cart_snapshot', 'cart_reservation_token', 'payment_start_time']);
    }

    /**
     * Manejo de pago fallido
     */
    public function handleFailedPayment(Payment $payment, ?string $errorCode = null, ?string $errorMessage = null): bool
    {
        $payment->markAsFailed($errorCode, $errorMessage);

        Log::warning('Payment failed', [
            'payment_id'   => $payment->payment_id,
            'booking_id'   => $payment->booking_id,
            'error_code'   => $errorCode,
            'error_message' => $errorMessage,
        ]);

        // TODO: correo de fallo de pago
        return true;
    }

    /**
     * Procesar reembolso
     */
    public function processRefund(Payment $payment, ?float $amount = null, array $data = []): array
    {
        if (!$payment->is_refundable) {
            throw new \Exception('Payment is not refundable');
        }

        $refundAmount = $amount ?? $payment->net_amount;

        if ($refundAmount > $payment->net_amount) {
            throw new \Exception('Refund amount exceeds available amount');
        }

        $gateway = $this->gatewayManager->driver($payment->gateway);

        try {
            $result = $gateway->refundPayment(
                $payment->gateway_transaction_id ?? $payment->gateway_payment_intent_id,
                $refundAmount,
                $data
            );

            $payment->recordRefund($refundAmount, $result);

            if ($payment->is_fully_refunded) {
                $booking = $payment->booking;
                if ($booking) {
                    $booking->update(['status' => 'cancelled']);
                }
            }

            Log::info('Refund processed', [
                'payment_id'    => $payment->payment_id,
                'refund_amount' => $refundAmount,
                'refund_id'     => $result['refund_id'] ?? null,
            ]);

            return [
                'success'   => true,
                'refund_id' => $result['refund_id'] ?? null,
                'amount'    => $refundAmount,
                'payment'   => $payment->fresh(),
            ];
        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'payment_id' => $payment->payment_id,
                'amount'     => $refundAmount,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Obtener status desde el gateway (Stripe, PayPal, etc.)
     */
    public function getPaymentStatus(Payment $payment): array
    {
        try {
            $gateway = $this->gatewayManager->driver($payment->gateway);
            return $gateway->getPaymentStatus($payment->gateway_payment_intent_id);
        } catch (\Exception $e) {
            Log::error('Failed to get payment status', [
                'payment_id' => $payment->payment_id,
                'gateway'    => $payment->gateway,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Enviar email de confirmación de reserva
     */
    protected function sendConfirmationEmail(Booking $booking, Payment $payment): void
    {
        $booking->load(['detail', 'user', 'tour']);

        $notify    = $this->getNotifyEmails();
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
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Correos de notificación
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
