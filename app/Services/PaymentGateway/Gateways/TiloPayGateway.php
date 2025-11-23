<?php

namespace App\Services\PaymentGateway\Gateways;

use App\Services\PaymentGateway\AbstractPaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TiloPay Gateway Implementation
 * 
 * TiloPay is a Costa Rican payment gateway supporting CRC and USD.
 * 
 * Required Configuration:
 * - merchant_id: Your TiloPay merchant ID
 * - api_key: API key from TiloPay dashboard
 * - api_secret: API secret for authentication
 * - webhook_secret: Secret for webhook signature verification
 * - base_url: API base URL (sandbox or production)
 * 
 * @see https://tilopay.com/
 * @see https://docs.tilopay.com/ (when available)
 */
class TiloPayGateway extends AbstractPaymentGateway
{
    protected string $gatewayName = 'tilopay';

    /**
     * Get API base URL
     */
    protected function getBaseUrl(): string
    {
        return $this->config['base_url'] ?? 'https://api.tilopay.com/v1';
    }

    /**
     * Make authenticated API request
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->{$method}($this->getBaseUrl() . $endpoint, $data);

            if (!$response->successful()) {
                throw new \Exception('TiloPay API error: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('TiloPay API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createPaymentIntent(array $data): array
    {
        // Prepare payment data
        $paymentData = [
            'merchant_id' => $this->config['merchant_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'CRC',
            'reference' => $data['booking_reference'] ?? uniqid('booking_'),
            'description' => $data['description'] ?? 'Tour Booking',
            'customer' => [
                'email' => $data['user_email'] ?? null,
                'name' => $data['customer_name'] ?? null,
            ],
            'return_url' => $data['return_url'] ?? route('payment.confirm'),
            'cancel_url' => $data['cancel_url'] ?? route('payment.cancel'),
            'webhook_url' => route('webhooks.payment.tilopay'),
        ];

        // Make API request
        $response = $this->makeRequest('post', '/payments', $paymentData);

        return [
            'success' => true,
            'payment_intent_id' => $response['transaction_id'] ?? $response['id'],
            'client_secret' => $response['token'] ?? null,
            'payment_url' => $response['payment_url'] ?? null,
            'status' => $response['status'] ?? 'pending',
        ];
    }

    public function capturePayment(string $paymentIntentId, array $data = []): array
    {
        $response = $this->makeRequest('post', "/payments/{$paymentIntentId}/capture", $data);

        return [
            'success' => true,
            'transaction_id' => $response['transaction_id'] ?? $paymentIntentId,
            'status' => $response['status'] ?? 'completed',
            'captured_amount' => $response['amount'] ?? null,
        ];
    }

    public function refundPayment(string $transactionId, float $amount, array $data = []): array
    {
        $refundData = [
            'amount' => $amount,
            'reason' => $data['reason'] ?? 'Customer request',
        ];

        $response = $this->makeRequest('post', "/payments/{$transactionId}/refund", $refundData);

        return [
            'success' => true,
            'refund_id' => $response['refund_id'] ?? $response['id'],
            'status' => 'refunded',
            'refunded_amount' => $amount,
        ];
    }

    public function getPaymentStatus(string $paymentIntentId): array
    {
        $response = $this->makeRequest('get', "/payments/{$paymentIntentId}");

        // Map TiloPay status to standard status
        $statusMap = [
            'pending' => 'pending',
            'processing' => 'processing',
            'completed' => 'succeeded',
            'approved' => 'succeeded',
            'failed' => 'failed',
            'cancelled' => 'canceled',
            'refunded' => 'refunded',
        ];

        $status = $statusMap[$response['status'] ?? 'pending'] ?? 'pending';

        return [
            'status' => $status,
            'transaction_id' => $response['transaction_id'] ?? $paymentIntentId,
            'amount' => $response['amount'] ?? null,
            'currency' => $response['currency'] ?? null,
            'payment_method' => [
                'type' => $response['payment_method']['type'] ?? 'card',
                'card_brand' => $response['payment_method']['card_brand'] ?? null,
                'card_last4' => $response['payment_method']['last4'] ?? null,
            ],
        ];
    }

    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        // Verify webhook signature
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

        // Map event types
        $eventTypeMap = [
            'payment.completed' => 'payment_intent.succeeded',
            'payment.approved' => 'payment_intent.succeeded',
            'payment.failed' => 'payment_intent.payment_failed',
            'payment.refunded' => 'charge.refunded',
        ];

        $eventType = $eventTypeMap[$payload['event'] ?? ''] ?? $payload['event'] ?? 'unknown';

        return [
            'success' => true,
            'event_type' => $eventType,
            'transaction_id' => $payload['transaction_id'] ?? $payload['data']['id'] ?? null,
            'status' => $payload['status'] ?? $payload['data']['status'] ?? null,
            'amount' => $payload['amount'] ?? $payload['data']['amount'] ?? null,
            'payment_data' => $payload['data'] ?? $payload,
        ];
    }

    public function validateCredentials(): bool
    {
        if (
            empty($this->config['merchant_id']) ||
            empty($this->config['api_key']) ||
            empty($this->config['api_secret'])
        ) {
            return false;
        }

        try {
            // Test API connection
            $this->makeRequest('get', '/merchant/info');
            return true;
        } catch (\Exception $e) {
            Log::warning('TiloPay credentials validation failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function supportsCurrency(string $currency): bool
    {
        // TiloPay supports CRC (Costa Rican Colón) and USD
        return in_array(strtoupper($currency), ['CRC', 'USD']);
    }

    public function createCustomer(array $customerData): array
    {
        $response = $this->makeRequest('post', '/customers', [
            'email' => $customerData['email'],
            'name' => $customerData['name'] ?? null,
            'phone' => $customerData['phone'] ?? null,
        ]);

        return [
            'success' => true,
            'customer_id' => $response['customer_id'] ?? $response['id'],
        ];
    }

    public function savePaymentMethod(string $customerId, string $paymentMethodId): array
    {
        $response = $this->makeRequest('post', "/customers/{$customerId}/payment-methods", [
            'payment_method_id' => $paymentMethodId,
        ]);

        return [
            'success' => true,
            'payment_method_id' => $response['payment_method_id'] ?? $response['id'],
        ];
    }
}
