<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\PaymentGateway\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected PaymentGatewayManager $gatewayManager
    ) {}

    /**
     * Handle Alignet Webhook / Callback
     */
    public function alignet(Request $request)
    {
        Log::info('📩 Alignet Webhook Received', $request->all());

        try {
            $operationNumber = $request->input('purchaseOperationNumber');
            $authorizationResult = $request->input('authorizationResult'); // 00 = Aprobado, 01 = Denegado, 05 = Rechazado

            if (!$operationNumber) {
                Log::error('Alignet Webhook: Missing operation number');
                return response()->json(['error' => 'Missing data'], 400);
            }

            // Validar firma (Security)
            $service = app(\App\Services\AlignetPaymentService::class);
            if (!$service->validateResponse($request->all())) {
                Log::error('Alignet Webhook: Invalid signature', $request->all());
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            // Buscar pago
            $payment = Payment::where('gateway_transaction_id', $operationNumber)->first();

            // Si no encuentra por operationNumber (porque lo guardamos diferente), buscar en reserved2 (payment_id)
            if (!$payment && $request->has('reserved2')) {
                $payment = Payment::find($request->input('reserved2'));
            }

            if (!$payment) {
                Log::error('Alignet Webhook: Payment not found', ['op' => $operationNumber]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            if ($authorizationResult === '00') {
                // ✅ ÉXITO
                if ($payment->status !== 'completed') {
                    $this->paymentService->handleSuccessfulPayment($payment, [
                        'transaction_id' => $operationNumber,
                        'authorization_code' => $request->input('authorizationCode'),
                        'card_brand' => $request->input('brand'),
                    ]);
                }

                Log::info('✅ Alignet Payment Confirmed via Webhook', ['payment_id' => $payment->payment_id]);

                // 🚦 RESPONSE STRATEGY:
                // Si es una petición del navegador (Browser Callback), redirigimos.
                // Si es S2S (Server to Server), retornamos JSON/Text.
                // VPOS2 suele hacer POST desde el navegador al final.

                // Detectamos si es AJAX o Navegador
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'OK']);
                }

                // Si es navegador, retornamos una página "Cerrar y Redirigir"
                // Ojo: Si estamos dentro del iframe, necesitamos JS para romper el iframe.
                return response('
                    <script>
                        window.top.location.href = "' . route('booking.confirmation', $payment->booking_id) . '";
                    </script>
                ');
            } else {
                // ❌ FALLO
                $errorCode = $request->input('errorCode');
                $errorMessage = $request->input('errorMessage');

                Log::warning('Alignet Payment Rejected', [
                    'payment_id' => $payment->payment_id,
                    'result' => $authorizationResult,
                    'error' => $errorMessage
                ]);

                $this->paymentService->handleFailedPayment(
                    $payment,
                    'alignet_rejected_' . $authorizationResult,
                    $errorMessage ?? 'Payment rejected by bank'
                );

                if ($request->wantsJson()) {
                    return response()->json(['status' => 'REJECTED']);
                }

                // Redirigir al carrito con información de error para mostrarla
                $redirectUrl = route('public.carts.index', [
                    'error' => $errorMessage ?? 'Su pago fue rechazado',
                    'details' => "Code: " . ($errorCode ?? $authorizationResult) . " - " . ($errorMessage ?? 'Unknown')
                ]);

                return response('
                    <script>
                        window.top.location.href = "' . $redirectUrl . '";
                    </script>
                ');
            }
        } catch (\Exception $e) {
            Log::error('Alignet Webhook Exception', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function stripe(Request $request)
    {
        try {
            // Get the signature header
            $signature = $request->header('Stripe-Signature');

            // Get raw payload
            $payload = $request->getContent();
            $data = json_decode($payload, true);

            // Handle webhook with gateway
            $gateway = $this->gatewayManager->driver('stripe');
            $event = $gateway->handleWebhook($data, $signature);

            Log::info('Stripe webhook received', [
                'type' => $event['event_type'],
                'id' => $event['event_id'] ?? null,
            ]);

            // Handle different event types
            switch ($event['event_type']) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event['data']->object ?? $data['data']['object']);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event['data']->object ?? $data['data']['object']);
                    break;

                case 'charge.refunded':
                    $this->handleRefund($event['data']->object ?? $data['data']['object']);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'type' => $event['event_type'],
                    ]);
            }

            return response()->json(['received' => true]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Webhook handler failed',
            ], 500);
        }
    }



    /**
     * Handle generic webhook event (for local gateways)
     */
    protected function handleGenericWebhookEvent(array $event): void
    {
        switch ($event['event_type']) {
            case 'payment_intent.succeeded':
                // Find payment by transaction ID
                $payment = Payment::where('gateway_transaction_id', $event['transaction_id'])
                    ->orWhere('gateway_payment_intent_id', $event['transaction_id'])
                    ->first();

                if ($payment && $payment->status !== 'completed') {
                    $this->paymentService->handleSuccessfulPayment($payment, $event['payment_data'] ?? []);
                }
                break;

            case 'payment_intent.payment_failed':
                $payment = Payment::where('gateway_transaction_id', $event['transaction_id'])
                    ->orWhere('gateway_payment_intent_id', $event['transaction_id'])
                    ->first();

                if ($payment && $payment->status !== 'failed') {
                    $this->paymentService->handleFailedPayment(
                        $payment,
                        'payment_failed',
                        $event['error_message'] ?? 'Payment failed'
                    );
                }
                break;

            case 'charge.refunded':
                $payment = Payment::where('gateway_transaction_id', $event['transaction_id'])
                    ->orWhere('gateway_payment_intent_id', $event['transaction_id'])
                    ->first();

                if ($payment) {
                    $refundAmount = $event['amount'] ?? $payment->amount;
                    $payment->update([
                        'amount_refunded' => $refundAmount,
                        'status' => $refundAmount >= $payment->amount ? 'refunded' : 'partially_refunded',
                        'refunded_at' => now(),
                    ]);
                }
                break;
        }
    }

    /**
     * Handle successful payment
     */
    protected function handlePaymentSucceeded($paymentIntent): void
    {
        $payment = Payment::where('gateway_payment_intent_id', $paymentIntent['id'] ?? $paymentIntent->id)
            ->first();

        if (!$payment) {
            Log::warning('Payment not found for successful payment intent', [
                'intent_id' => $paymentIntent['id'] ?? $paymentIntent->id,
            ]);
            return;
        }

        // Skip if already processed
        if ($payment->status === 'completed') {
            return;
        }

        $this->paymentService->handleSuccessfulPayment($payment, [
            'transaction_id' => $paymentIntent['id'] ?? $paymentIntent->id,
            'status' => $paymentIntent['status'] ?? $paymentIntent->status,
        ]);

        Log::info('Payment marked as successful via webhook', [
            'payment_id' => $payment->payment_id,
            'booking_id' => $payment->booking_id,
        ]);
    }

    /**
     * Handle failed payment
     */
    protected function handlePaymentFailed($paymentIntent): void
    {
        $payment = Payment::where('gateway_payment_intent_id', $paymentIntent['id'] ?? $paymentIntent->id)
            ->first();

        if (!$payment) {
            Log::warning('Payment not found for failed payment intent', [
                'intent_id' => $paymentIntent['id'] ?? $paymentIntent->id,
            ]);
            return;
        }

        // Skip if already processed
        if ($payment->status === 'failed') {
            return;
        }

        $lastError = $paymentIntent['last_payment_error'] ?? $paymentIntent->last_payment_error ?? null;

        $this->paymentService->handleFailedPayment(
            $payment,
            $lastError['code'] ?? 'payment_failed',
            $lastError['message'] ?? 'Payment failed'
        );

        Log::info('Payment marked as failed via webhook', [
            'payment_id' => $payment->payment_id,
            'booking_id' => $payment->booking_id,
        ]);
    }

    /**
     * Handle refund
     */
    protected function handleRefund($charge): void
    {
        // Find payment by charge ID or payment intent ID
        $paymentIntentId = $charge['payment_intent'] ?? $charge->payment_intent ?? null;

        if (!$paymentIntentId) {
            Log::warning('No payment intent ID in refund webhook');
            return;
        }

        $payment = Payment::where('gateway_payment_intent_id', $paymentIntentId)
            ->first();

        if (!$payment) {
            Log::warning('Payment not found for refund', [
                'payment_intent_id' => $paymentIntentId,
            ]);
            return;
        }

        // Get refund amount
        $refundedAmount = ($charge['amount_refunded'] ?? $charge->amount_refunded ?? 0) / 100;

        if ($refundedAmount > 0) {
            $payment->update([
                'amount_refunded' => $refundedAmount,
                'status' => $refundedAmount >= $payment->amount ? 'refunded' : 'partially_refunded',
                'refunded_at' => now(),
            ]);

            Log::info('Refund processed via webhook', [
                'payment_id' => $payment->payment_id,
                'refund_amount' => $refundedAmount,
            ]);
        }
    }
}
