<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\PaymentGateway\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Keeping Log for low-level debug if needed, but primary is LoggerHelper
use App\Services\LoggerHelper;

/**
 * PaymentWebhookController
 *
 * Processes payment gateway webhooks.
 */
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
        LoggerHelper::info('PaymentWebhookController', 'alignet', 'Webhook Received', [
            'all_params' => $request->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'method' => $request->method(),
        ]);

        try {
            // Instantiate the service early for use throughout
            $service = app(\App\Services\AlignetPaymentService::class);

            $operationNumber = $request->input('purchaseOperationNumber');
            $authorizationResult = $request->input('authorizationResult') ?? '';
            $errorCode = $request->input('errorCode');
            $errorMessage = $request->input('errorMessage');

            // Get detailed code info for logging
            $codeInfo = $service->getLogContext($authorizationResult, $errorCode, $errorMessage);

            if (!$operationNumber) {
                LoggerHelper::warning('PaymentWebhookController', 'alignet', 'Missing operation number', $request->all());
                return response()->json(['error' => 'Missing data'], 400);
            }

            // Validar firma (Security)
            $purchaseVerification = $request->input('purchaseVerification');
            $skipSignature = false;

            // Lógica Granular de Validación de Firma
            if (empty($purchaseVerification)) {
                // Caso 1: Rechazo/Cancelación sin firma -> Permitir (Bypass)
                // Incluimos 01 (Denegado), 05 (Rechazado), y otros códigos de fallo
                if (in_array($authorizationResult, ['01', '05', '06', '07'])) {
                    LoggerHelper::warning('PaymentWebhookController', 'alignet', 'Empty signature for rejected/cancelled transaction - Bypassing check', [
                        'auth' => $authorizationResult,
                        'op' => $operationNumber
                    ]);
                    $skipSignature = true;
                } else {
                    // Caso 2: Éxito (00) o desconocido sin firma -> Error de Seguridad
                    LoggerHelper::exception('PaymentWebhookController', 'alignet', 'Security', null, new \Exception('Missing signature for critical transaction'), ['auth' => $authorizationResult]);
                    $securityMsg = __('m_checkout.payment.operation_rejected');
                    return $this->renderModalResponse($request, 'error', $securityMsg, route('public.carts.index', ['error' => $securityMsg]));
                }
            }

            // Si hay firma (o no es bypass), validamos estrictamente
            if (!$skipSignature && !$service->validateResponse($request->all())) {
                LoggerHelper::warning('PaymentWebhookController', 'alignet', 'Invalid signature', $request->all());

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
                LoggerHelper::error('PaymentWebhookController', 'alignet', 'Payment not found', ['op' => $operationNumber]);
                return $this->renderModalResponse($request, 'error', 'Pago no encontrado', route('public.carts.index', ['error' => 'Payment Not Found. Op: ' . $operationNumber]));
            }

            // [SECURITY] Validate Amount to prevent tampering
            // Alignet sends amount in cents (e.g. 10000 for $100.00)
            $receivedAmount = $request->input('purchaseAmount');
            $expectedAmount = (int)round($payment->amount * 100);

            // We only validate amount if it's present and operation is approved to avoid false positives on chaotic rejections
            if ($authorizationResult === '00' && $receivedAmount && (int)$receivedAmount !== $expectedAmount) {
                LoggerHelper::error('PaymentWebhookController', 'alignet', 'Security: Amount mismatch', [
                    'expected' => $expectedAmount,
                    'received' => $receivedAmount,
                    'payment_id' => $payment->payment_id,
                    'op' => $operationNumber
                ]);

                return $this->renderModalResponse(
                    $request,
                    'error',
                    'Error de seguridad: Monto inválido',
                    route('public.carts.index', ['error' => 'Security Error: Amount Mismatch'])
                );
            }

            // LÓGICA DE ESTADOS
            // Check if authorized using the new helper
            if ($service->isAuthorized($authorizationResult)) {
                // SUCCESS (00, 09, 10, 11)
                if ($payment->status !== 'completed') {
                    $this->paymentService->handleSuccessfulPayment($payment, [
                        'transaction_id' => $operationNumber,
                        'authorization_code' => $request->input('authorizationCode'),
                        'card_brand' => $request->input('brand'),
                    ]);
                }

                LoggerHelper::info('PaymentWebhookController', 'alignet', 'Payment Authorized', array_merge(
                    ['payment_id' => $payment->payment_id, 'booking_id' => $payment->booking_id],
                    $codeInfo
                ));

                return $this->renderModalResponse($request, 'success', __('m_checkout.payment.success'), route('booking.confirmation', $payment->booking_id));
            } elseif ($service->getClassification($authorizationResult) === 'cancelled' || in_array($errorCode, ['2300', '2301', '2302'])) {
                // CANCELLED by user
                LoggerHelper::info('PaymentWebhookController', 'alignet', 'Payment Cancelled', array_merge(
                    ['payment_id' => $payment->payment_id],
                    $codeInfo
                ));

                $this->paymentService->handleFailedPayment($payment, 'user_cancelled', $service->getDescription($authorizationResult, 'en'));

                $cancelMsg = __('m_checkout.payment.cancelled_by_user');
                return $this->renderModalResponse($request, 'cancel', $cancelMsg, route('public.carts.index', ['cancelled' => '1']));
            } else {
                // FAILURE - Denied, Rejected, or Error
                $classification = $service->getClassification($authorizationResult);
                $codeDescription = $service->getDescription($authorizationResult, app()->getLocale());

                // Enhanced logging with full code details
                LoggerHelper::warning('PaymentWebhookController', 'alignet', 'Payment Failed', array_merge(
                    ['payment_id' => $payment->payment_id, 'booking_id' => $payment->booking_id],
                    $codeInfo
                ));

                $this->paymentService->handleFailedPayment(
                    $payment,
                    'alignet_' . $classification . '_' . $authorizationResult,
                    $service->getDescription($authorizationResult, 'en')
                );

                // Get user message based on classification
                $userMessage = $service->getUserMessage($authorizationResult, app()->getLocale());

                // In debug mode, show detailed code information
                if (config('app.debug')) {
                    $debug = "[{$authorizationResult}] {$codeDescription}";
                    if ($errorCode) {
                        $errorCodeInfo = $service->getResponseCodeInfo($errorCode);
                        $debug .= "\nError Code: [{$errorCode}] " . ($errorCodeInfo['es'] ?? $errorMessage ?? 'N/A');
                    }
                    return $this->renderModalResponse($request, 'error', $userMessage . "\n\nDEBUG:\n" . $debug, route('public.carts.index', ['error' => $userMessage]));
                }

                return $this->renderModalResponse($request, 'error', $userMessage, route('public.carts.index', ['error' => $userMessage]));
            }
        } catch (\Exception $e) {
            LoggerHelper::exception('PaymentWebhookController', 'alignet', 'System', null, $e);
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

            LoggerHelper::info('PaymentWebhookController', 'stripe', 'Webhook received', [
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
            LoggerHelper::exception('PaymentWebhookController', 'stripe', 'Webhook', null, $e);

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
            LoggerHelper::warning('PaymentWebhookController', 'handlePaymentSucceeded', 'Payment not found for successful payment intent', [
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

        LoggerHelper::info('PaymentWebhookController', 'handlePaymentSucceeded', 'Payment marked as successful via webhook', [
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
            LoggerHelper::warning('PaymentWebhookController', 'handlePaymentFailed', 'Payment not found for failed payment intent', [
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

        LoggerHelper::info('PaymentWebhookController', 'handlePaymentFailed', 'Payment marked as failed via webhook', [
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
            LoggerHelper::warning('PaymentWebhookController', 'handleRefund', 'No payment intent ID in refund webhook');
            return;
        }

        $payment = Payment::where('gateway_payment_intent_id', $paymentIntentId)
            ->first();

        if (!$payment) {
            LoggerHelper::warning('PaymentWebhookController', 'handleRefund', 'Payment not found for refund', [
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

            LoggerHelper::info('PaymentWebhookController', 'handleRefund', 'Refund processed via webhook', [
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
