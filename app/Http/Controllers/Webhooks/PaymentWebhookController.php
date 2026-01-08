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
        // Enhanced logging with full request details
        Log::info('📩 Alignet Webhook - Full Data', [
            'all_params' => $request->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'method' => $request->method(),
        ]);

        try {
            $operationNumber = $request->input('purchaseOperationNumber');
            $authorizationResult = $request->input('authorizationResult'); // 00 = Aprobado, 01 = Denegado, 05 = Rechazado

            if (!$operationNumber) {
                Log::error('Alignet Webhook: Missing operation number');
                return response()->json(['error' => 'Missing data'], 400);
            }

            // Validar firma (Security)
            $service = app(\App\Services\AlignetPaymentService::class);
            $purchaseVerification = $request->input('purchaseVerification');
            $authorizationResult = $request->input('authorizationResult');
            $skipSignature = false;

            // Lógica Granular de Validación de Firma
            if (empty($purchaseVerification)) {
                // Caso 1: Rechazo/Cancelación sin firma -> Permitir (Bypass)
                // Incluimos 01 (Denegado), 05 (Rechazado), y otros códigos de fallo
                if (in_array($authorizationResult, ['01', '05', '06', '07'])) {
                    Log::warning('Alignet: Empty signature for rejected/cancelled transaction - Bypassing check', [
                        'auth' => $authorizationResult,
                        'op' => $operationNumber
                    ]);
                    $skipSignature = true;
                } else {
                    // Caso 2: Éxito (00) o desconocido sin firma -> Error de Seguridad
                    Log::error('Alignet Webhook: Missing signature for critical transaction', ['auth' => $authorizationResult]);
                    $securityMsg = __('m_checkout.payment.operation_rejected');
                    return $this->renderModalResponse($request, 'error', $securityMsg, route('public.carts.index', ['error' => $securityMsg]));
                }
            }

            // Si hay firma (o no es bypass), validamos estrictamente
            if (!$skipSignature && !$service->validateResponse($request->all())) {
                Log::error('Alignet Webhook: Invalid signature', $request->all());

                $securityMsg = __('m_checkout.payment.operation_rejected');

                // Only add debug info if in debug mode
                if (config('app.debug')) {
                    $debug = "AuthResult: " . ($authorizationResult ?? 'N/A') .
                        ", ErrorCode: " . ($request->input('errorCode') ?? 'N/A') .
                        ", ErrorMsg: " . ($request->input('errorMessage') ?? 'N/A');
                    return $this->renderModalResponse($request, 'error', $securityMsg . "\n\nDEBUG: " . $debug, route('public.carts.index', ['error' => $securityMsg . "\n\nDEBUG: " . $debug]));
                }

                return $this->renderModalResponse($request, 'error', $securityMsg, route('public.carts.index', ['error' => $securityMsg]));
            }

            // Buscar pago
            $payment = Payment::where('gateway_transaction_id', $operationNumber)->first();

            // Si no encuentra por operationNumber (porque lo guardamos diferente), buscar en reserved2 (payment_id)
            if (!$payment && $request->has('reserved2')) {
                $payment = Payment::find($request->input('reserved2'));
            }

            if (!$payment) {
                Log::error('Alignet Webhook: Payment not found', ['op' => $operationNumber]);
                return $this->renderModalResponse($request, 'error', 'Pago no encontrado', route('public.carts.index', ['error' => 'Payment Not Found. Op: ' . $operationNumber]));
            }

            // 🚦 LÓGICA DE ESTADOS
            if ($authorizationResult === '00') {
                // ✅ ÉXITO (00)
                if ($payment->status !== 'completed') {
                    $this->paymentService->handleSuccessfulPayment($payment, [
                        'transaction_id' => $operationNumber,
                        'authorization_code' => $request->input('authorizationCode'),
                        'card_brand' => $request->input('brand'),
                    ]);
                }

                Log::info('✅ Alignet Payment Confirmed', ['payment_id' => $payment->payment_id]);
                return $this->renderModalResponse($request, 'success', __('m_checkout.payment.success'), route('booking.confirmation', $payment->booking_id));
            } elseif ($authorizationResult === '99' || $request->input('errorCode') == '2401') {
                // 🚫 CANCELADO (99 o 2401 VbV Cancel)

                // Specific logging for error 2401 (Pre Auth Rules)
                if ($request->input('errorCode') == '2401') {
                    Log::warning('⚠️ Error 2401 - Pre Auth Rules', [
                        'operation' => $operationNumber,
                        'commerce_id' => $request->input('idCommerce'),
                        'VCI' => $request->input('VCI'),
                        'ECI' => $request->input('ECI'),
                        'card_bin' => $request->input('bin'),
                        'brand' => $request->input('brand'),
                        'shipping_country' => $request->input('shippingCountry'),
                        'billing_country' => $request->input('billingCountry'),
                    ]);
                } else {
                    Log::info('Alignet Payment Cancelled', ['payment_id' => $payment->payment_id]);
                }

                // Opcional: Marcar como cancelado en BD si queremos
                $this->paymentService->handleFailedPayment($payment, 'user_cancelled', 'User cancelled payment');

                // Improved cancellation message
                $cancelMsg = __('m_checkout.payment.cancelled_by_user');
                return $this->renderModalResponse($request, 'cancel', $cancelMsg, route('public.carts.index', ['cancelled' => '1']));
            } else {
                // ❌ FALLO (Cualquier otro código)
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

                // Mapeo básico de errores comunes
                if ($authorizationResult === '01') {
                    $userMessage = __('m_checkout.payment.operation_denied');
                } elseif ($authorizationResult === '05') {
                    $userMessage = __('m_checkout.payment.operation_rejected');
                } else {
                    $userMessage = $errorMessage ?? __('m_checkout.payment.failed');
                }

                // Only add debug info if in debug mode
                if (config('app.debug')) {
                    $debug = "Auth: " . ($authorizationResult ?? '?') .
                        ", Code: " . ($errorCode ?? '?') .
                        ", Msg: " . ($errorMessage ?? 'N/A');
                    return $this->renderModalResponse($request, 'error', $userMessage, route('public.carts.index', ['error' => $userMessage . "\n\nDEBUG: " . $debug]));
                }

                return $this->renderModalResponse($request, 'error', $userMessage, route('public.carts.index', ['error' => $userMessage]));
            }
        } catch (\Exception $e) {
            Log::error('Alignet Webhook Exception', ['error' => $e->getMessage()]);
            return $this->renderModalResponse($request, 'error', 'Error del Sistema', route('public.carts.index', ['error' => 'Server Error']));
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
    /**
     * Helper para responder HTML o JSON según contexto
     */
    private function renderModalResponse(Request $request, string $type, string $message, string $redirectUrl)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'status' => $type,
                'message' => $message,
                'redirect_url' => $redirectUrl
            ]);
        }

        return view('payments.alignet-response', [
            'type' => $type,
            'message' => $message,
            'redirectUrl' => $redirectUrl
        ]);
    }
}
