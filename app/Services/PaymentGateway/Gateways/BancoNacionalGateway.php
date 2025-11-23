<?php

namespace App\Services\PaymentGateway\Gateways;

use App\Services\PaymentGateway\AbstractPaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Banco Nacional Gateway Implementation
 * 
 * Banco Nacional de Costa Rica payment gateway.
 * Supports CRC and USD transactions.
 * 
 * Required Configuration:
 * - merchant_id: Merchant ID from Banco Nacional
 * - terminal_id: Terminal ID
 * - api_key: API key for authentication
 * - api_secret: API secret
 * - webhook_secret: Secret for webhook verification
 * - base_url: API base URL (sandbox or production)
 * 
 * @see https://www.bncr.fi.cr/
 */
class BancoNacionalGateway extends AbstractPaymentGateway
{
    protected string $gatewayName = 'banco_nacional';

    protected function getBaseUrl(): string
    {
        return $this->config['base_url'] ?? 'https://api.bncr.fi.cr/payments/v1';
    }

    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'X-Terminal-ID' => $this->config['terminal_id'] ?? '',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->{$method}($this->getBaseUrl() . $endpoint, $data);

            if (!$response->successful()) {
                throw new \Exception('Banco Nacional API error: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Banco Nacional API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createPaymentIntent(array $data): array
    {
        $paymentData = [
            'merchant_id' => $this->config['merchant_id'],
            'terminal_id' => $this->config['terminal_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'CRC',
            'order_id' => $data['booking_reference'] ?? uniqid('order_'),
            'description' => $data['description'] ?? 'Tour Booking',
            'customer_email' => $data['user_email'] ?? null,
            'return_url' => $data['return_url'] ?? route('payment.confirm'),
            'cancel_url' => $data['cancel_url'] ?? route('payment.cancel'),
            'notification_url' => route('webhooks.payment.banco_nacional'),
        ];

        $response = $this->makeRequest('post', '/transactions', $paymentData);

        return [
            'success' => true,
            'payment_intent_id' => $response['transaction_id'] ?? $response['id'],
            'client_secret' => $response['token'] ?? null,
            'payment_url' => $response['redirect_url'] ?? null,
            'status' => 'pending',
        ];
    }

    public function capturePayment(string $paymentIntentId, array $data = []): array
    {
        $response = $this->makeRequest('post', "/transactions/{$paymentIntentId}/confirm", $data);

        return [
            'success' => true,
            'transaction_id' => $response['transaction_id'] ?? $paymentIntentId,
            'status' => 'completed',
            'authorization_code' => $response['authorization_code'] ?? null,
        ];
    }

    public function refundPayment(string $transactionId, float $amount, array $data = []): array
    {
        $refundData = [
            'amount' => $amount,
            'reason' => $data['reason'] ?? 'Customer request',
        ];

        $response = $this->makeRequest('post', "/transactions/{$transactionId}/refund", $refundData);

        return [
            'success' => true,
            'refund_id' => $response['refund_id'] ?? $response['id'],
            'status' => 'refunded',
            'refunded_amount' => $amount,
        ];
    }

    public function getPaymentStatus(string $paymentIntentId): array
    {
        $response = $this->makeRequest('get', "/transactions/{$paymentIntentId}");

        $statusMap = [
            'pending' => 'pending',
            'authorized' => 'processing',
            'approved' => 'succeeded',
            'completed' => 'succeeded',
            'declined' => 'failed',
            'cancelled' => 'canceled',
            'refunded' => 'refunded',
        ];

        $status = $statusMap[$response['status'] ?? 'pending'] ?? 'pending';

        return [
            'status' => $status,
            'transaction_id' => $response['transaction_id'] ?? $paymentIntentId,
            'amount' => $response['amount'] ?? null,
            'currency' => $response['currency'] ?? null,
            'authorization_code' => $response['authorization_code'] ?? null,
            'payment_method' => [
                'type' => 'card',
                'card_brand' => $response['card_brand'] ?? null,
                'card_last4' => $response['card_last4'] ?? null,
            ],
        ];
    }

    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        if ($signature && !empty($this->config['webhook_secret'])) {
            $expectedSignature = hash_hmac(
                'sha256',
                json_encode($payload),
                $this->config['webhook_secret']
            );

            if (!hash_equals($expectedSignature, $signature)) {
                throw new \Exception('Invalid webhook signature');
            }
        }

        $eventTypeMap = [
            'transaction.approved' => 'payment_intent.succeeded',
            'transaction.declined' => 'payment_intent.payment_failed',
            'transaction.refunded' => 'charge.refunded',
        ];

        $eventType = $eventTypeMap[$payload['event_type'] ?? ''] ?? 'unknown';

        return [
            'success' => true,
            'event_type' => $eventType,
            'transaction_id' => $payload['transaction_id'] ?? null,
            'status' => $payload['status'] ?? null,
            'amount' => $payload['amount'] ?? null,
            'payment_data' => $payload,
        ];
    }

    public function validateCredentials(): bool
    {
        if (
            empty($this->config['merchant_id']) ||
            empty($this->config['terminal_id']) ||
            empty($this->config['api_key'])
        ) {
            return false;
        }

        try {
            $this->makeRequest('get', '/merchant/status');
            return true;
        } catch (\Exception $e) {
            Log::warning('Banco Nacional credentials validation failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function supportsCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), ['CRC', 'USD']);
    }

    public function createCustomer(array $customerData): array
    {
        // Banco Nacional may not support customer profiles
        // Return success with email as customer_id
        return [
            'success' => true,
            'customer_id' => $customerData['email'],
        ];
    }

    public function savePaymentMethod(string $customerId, string $paymentMethodId): array
    {
        // Not typically supported by bank gateways
        throw new \Exception('Payment method saving is not supported by Banco Nacional');
    }
}
