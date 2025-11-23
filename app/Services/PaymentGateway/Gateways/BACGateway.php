<?php

namespace App\Services\PaymentGateway\Gateways;

use App\Services\PaymentGateway\AbstractPaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * BAC Credomatic Gateway Implementation
 * 
 * BAC Credomatic payment gateway for Costa Rica.
 * Supports CRC and USD transactions.
 * 
 * Required Configuration:
 * - merchant_id: Merchant ID from BAC
 * - api_key: API key
 * - api_secret: API secret
 * - webhook_secret: Webhook verification secret
 * - base_url: API base URL
 * 
 * @see https://www.baccredomatic.com/
 */
class BACGateway extends AbstractPaymentGateway
{
    protected string $gatewayName = 'bac';

    protected function getBaseUrl(): string
    {
        return $this->config['base_url'] ?? 'https://api.baccredomatic.com/v1';
    }

    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'X-Merchant-ID' => $this->config['merchant_id'] ?? '',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->{$method}($this->getBaseUrl() . $endpoint, $data);

            if (!$response->successful()) {
                throw new \Exception('BAC API error: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('BAC API request failed', [
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
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'CRC',
            'reference' => $data['booking_reference'] ?? uniqid('ref_'),
            'description' => $data['description'] ?? 'Tour Booking',
            'customer' => [
                'email' => $data['user_email'] ?? null,
                'name' => $data['customer_name'] ?? null,
            ],
            'return_url' => $data['return_url'] ?? route('payment.confirm'),
            'cancel_url' => $data['cancel_url'] ?? route('payment.cancel'),
            'webhook_url' => route('webhooks.payment.bac'),
        ];

        $response = $this->makeRequest('post', '/payments', $paymentData);

        return [
            'success' => true,
            'payment_intent_id' => $response['payment_id'] ?? $response['id'],
            'client_secret' => $response['token'] ?? null,
            'payment_url' => $response['payment_url'] ?? null,
            'status' => 'pending',
        ];
    }

    public function capturePayment(string $paymentIntentId, array $data = []): array
    {
        $response = $this->makeRequest('post', "/payments/{$paymentIntentId}/capture", $data);

        return [
            'success' => true,
            'transaction_id' => $response['transaction_id'] ?? $paymentIntentId,
            'status' => 'completed',
            'authorization_code' => $response['auth_code'] ?? null,
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

        $statusMap = [
            'pending' => 'pending',
            'processing' => 'processing',
            'approved' => 'succeeded',
            'completed' => 'succeeded',
            'failed' => 'failed',
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
            'payment_method' => [
                'type' => 'card',
                'card_brand' => $response['card_type'] ?? null,
                'card_last4' => $response['last_four'] ?? null,
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
            'payment.approved' => 'payment_intent.succeeded',
            'payment.completed' => 'payment_intent.succeeded',
            'payment.failed' => 'payment_intent.payment_failed',
            'payment.refunded' => 'charge.refunded',
        ];

        $eventType = $eventTypeMap[$payload['event'] ?? ''] ?? 'unknown';

        return [
            'success' => true,
            'event_type' => $eventType,
            'transaction_id' => $payload['payment_id'] ?? $payload['transaction_id'] ?? null,
            'status' => $payload['status'] ?? null,
            'amount' => $payload['amount'] ?? null,
            'payment_data' => $payload,
        ];
    }

    public function validateCredentials(): bool
    {
        if (
            empty($this->config['merchant_id']) ||
            empty($this->config['api_key'])
        ) {
            return false;
        }

        try {
            $this->makeRequest('get', '/merchant/info');
            return true;
        } catch (\Exception $e) {
            Log::warning('BAC credentials validation failed', [
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
        throw new \Exception('Payment method saving is not supported by BAC');
    }
}
