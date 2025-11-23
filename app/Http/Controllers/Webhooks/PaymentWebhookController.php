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
     * Handle TiloPay webhook
     */
    public function tilopay(Request $request)
    {
        try {
            $signature = $request->header('X-TiloPay-Signature');
            $payload = $request->all();

            $gateway = $this->gatewayManager->driver('tilopay');
            $event = $gateway->handleWebhook($payload, $signature);

            Log::info('TiloPay webhook received', [
                'type' => $event['event_type'],
                'transaction_id' => $event['transaction_id'] ?? null,
            ]);

            // Handle event
            $this->handleGenericWebhookEvent($event);

            return response()->json(['received' => true]);
        } catch (\Exception $e) {
            Log::error('TiloPay webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook handler failed'], 500);
        }
    }

    /**
     * Handle Banco Nacional webhook
     */
    public function bancoNacional(Request $request)
    {
        try {
            $signature = $request->header('X-BN-Signature');
            $payload = $request->all();

            $gateway = $this->gatewayManager->driver('banco_nacional');
            $event = $gateway->handleWebhook($payload, $signature);

            Log::info('Banco Nacional webhook received', [
                'type' => $event['event_type'],
                'transaction_id' => $event['transaction_id'] ?? null,
            ]);

            $this->handleGenericWebhookEvent($event);

            return response()->json(['received' => true]);
        } catch (\Exception $e) {
            Log::error('Banco Nacional webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook handler failed'], 500);
        }
    }

    /**
     * Handle BAC webhook
     */
    public function bac(Request $request)
    {
        try {
            $signature = $request->header('X-BAC-Signature');
            $payload = $request->all();

            $gateway = $this->gatewayManager->driver('bac');
            $event = $gateway->handleWebhook($payload, $signature);

            Log::info('BAC webhook received', [
                'type' => $event['event_type'],
                'transaction_id' => $event['transaction_id'] ?? null,
            ]);

            $this->handleGenericWebhookEvent($event);

            return response()->json(['received' => true]);
        } catch (\Exception $e) {
            Log::error('BAC webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook handler failed'], 500);
        }
    }

    /**
     * Handle BCR webhook
     */
    public function bcr(Request $request)
    {
        try {
            $signature = $request->header('X-BCR-Signature');
            $payload = $request->all();

            $gateway = $this->gatewayManager->driver('bcr');
            $event = $gateway->handleWebhook($payload, $signature);

            Log::info('BCR webhook received', [
                'type' => $event['event_type'],
                'transaction_id' => $event['transaction_id'] ?? null,
            ]);

            $this->handleGenericWebhookEvent($event);

            return response()->json(['received' => true]);
        } catch (\Exception $e) {
            Log::error('BCR webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook handler failed'], 500);
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
