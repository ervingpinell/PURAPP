<?php

namespace App\Services\PaymentGateway\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Create a payment intent/session
     *
     * @param array $data Payment data including amount, currency, booking info
     * @return array Payment intent data with client_secret, intent_id, etc.
     * @throws \Exception
     */
    public function createPaymentIntent(array $data): array;

    /**
     * Capture/confirm a payment
     *
     * @param string $paymentIntentId Payment intent ID from gateway
     * @param array $data Additional data if needed
     * @return array Payment result with status, transaction_id, etc.
     * @throws \Exception
     */
    public function capturePayment(string $paymentIntentId, array $data = []): array;

    /**
     * Refund a payment (full or partial)
     *
     * @param string $transactionId Original transaction ID
     * @param float $amount Amount to refund
     * @param array $data Additional refund data
     * @return array Refund result with refund_id, status, etc.
     * @throws \Exception
     */
    public function refundPayment(string $transactionId, float $amount, array $data = []): array;

    /**
     * Get payment status from gateway
     *
     * @param string $paymentIntentId Payment intent ID
     * @return array Payment status data
     * @throws \Exception
     */
    public function getPaymentStatus(string $paymentIntentId): array;

    /**
     * Handle webhook from payment gateway
     *
     * @param array $payload Webhook payload
     * @param string|null $signature Webhook signature for verification
     * @return array Processed webhook data
     * @throws \Exception
     */
    public function handleWebhook(array $payload, ?string $signature = null): array;

    /**
     * Validate gateway credentials/configuration
     *
     * @return bool True if credentials are valid
     * @throws \Exception
     */
    public function validateCredentials(): bool;

    /**
     * Get gateway name
     *
     * @return string Gateway identifier (stripe, tilopay, etc.)
     */
    public function getGatewayName(): string;

    /**
     * Check if gateway supports a specific currency
     *
     * @param string $currency Currency code (USD, CRC, etc.)
     * @return bool
     */
    public function supportsCurrency(string $currency): bool;

    /**
     * Create or retrieve a customer in the gateway
     *
     * @param array $customerData Customer information
     * @return array Customer data with gateway_customer_id
     * @throws \Exception
     */
    public function createCustomer(array $customerData): array;

    /**
     * Save a payment method for future use
     *
     * @param string $customerId Gateway customer ID
     * @param string $paymentMethodId Gateway payment method ID
     * @return array Saved payment method data
     * @throws \Exception
     */
    public function savePaymentMethod(string $customerId, string $paymentMethodId): array;
}
