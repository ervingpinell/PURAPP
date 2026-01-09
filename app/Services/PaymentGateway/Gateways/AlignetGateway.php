<?php

namespace App\Services\PaymentGateway\Gateways;

use App\Services\PaymentGateway\AbstractPaymentGateway;
use App\Services\PaymentGateway\DTO\PaymentIntentResponse;
use App\Services\AlignetPaymentService;
use Exception;

/**
 * AlignetGateway
 *
 * Alignet payment gateway integration.
 */
class AlignetGateway extends AbstractPaymentGateway
{
    protected string $gatewayName = 'alignet';
    protected AlignetPaymentService $alignetService;

    public function __construct(array $config)
    {
        parent::__construct($config);

        // Validate required config
        $this->validateConfig(['acquirer_id', 'commerce_id', 'secret_key']);

        // Initialize Alignet service
        $this->alignetService = new AlignetPaymentService();
    }

    /**
     * Create a payment intent (prepare payment data for VPOS2)
     */
    public function createPaymentIntent(array $data): PaymentIntentResponse
    {
        try {
            $this->validateAmount($data['amount']);
            $this->validateCurrency($data['currency']);

            // Generate unique operation number
            $operationNumber = $this->generateOperationNumber($data['booking_id'] ?? null);

            // Prepare customer data
            $customerData = [
                'first_name' => $data['customer_first_name'] ?? '',
                'last_name' => $data['customer_last_name'] ?? '',
                'email' => $data['user_email'] ?? '',
                'address' => $data['customer_address'] ?? '',
                'city' => $data['customer_city'] ?? '',
                'zip' => $data['customer_zip'] ?? '',
                'state' => $data['customer_state'] ?? '',
                'country' => $data['customer_country'] ?? 'CR',
                'phone' => $data['customer_phone'] ?? '', // Add phone number
                'description' => $data['description'] ?? 'Tour booking',
                'booking_id' => $data['booking_id'] ?? '',
                'payment_id' => $data['payment_id'] ?? '', // 🔥 CRITICAL: Pass payment_id for reserved2
            ];

            // Prepare payment data
            $paymentData = $this->alignetService->preparePaymentData(
                $operationNumber,
                $data['amount'],
                $customerData,
                $data['user_code_payme'] ?? null
            );

            $this->logActivity('payment_intent_created', [
                'operation_number' => $operationNumber,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
            ]);

            return new PaymentIntentResponse(
                paymentIntentId: $operationNumber,
                status: 'pending',
                clientSecret: null,
                redirectUrl: null,
                metadata: [
                    'operation_number' => $operationNumber,
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'payment_data' => $paymentData, // IMPORTANT: Save payment_data here
                ],
                raw: $paymentData // Also save here
            );
        } catch (Exception $e) {
            $this->handleException($e, 'create_payment_intent');
        }
    }
    /**
     * Capture payment (not applicable for Alignet - payment is captured immediately)
     */
    public function capturePayment(string $paymentIntentId, array $data = []): array
    {
        // Alignet payments are captured immediately, so we just query the status
        return $this->getPaymentStatus($paymentIntentId);
    }

    /**
     * Refund payment
     */
    public function refundPayment(string $transactionId, float $amount, array $data = []): array
    {
        try {
            $this->validateAmount($amount);

            $this->logActivity('refund_requested', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);

            // Note: Alignet refunds typically need to be processed manually through their portal
            // This is a placeholder for logging purposes
            throw new Exception('Alignet refunds must be processed manually through the Alignet portal');
        } catch (Exception $e) {
            $this->handleException($e, 'refund_payment');
        }
    }

    /**
     * Get payment status by querying Alignet API
     */
    public function getPaymentStatus(string $paymentIntentId): array
    {
        try {
            $result = $this->alignetService->queryTransaction($paymentIntentId);

            if (!$result) {
                return [
                    'success' => false,
                    'status' => 'unknown',
                    'error' => 'Unable to query transaction status',
                ];
            }

            $this->logActivity('payment_status_retrieved', [
                'operation_number' => $paymentIntentId,
                'result' => $result,
            ]);

            return [
                'success' => true,
                'status' => $this->mapAlignetStatus($result),
                'amount' => isset($result['amount']) ? ($result['amount'] / 100) : null,
                'currency' => $result['currency'] ?? 'USD',
                'raw' => $result,
            ];
        } catch (Exception $e) {
            $this->handleException($e, 'get_payment_status');
        }
    }

    /**
     * Handle webhook/response from VPOS2
     */
    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        try {
            $this->logActivity('webhook_received', [
                'operation_number' => $payload['purchaseOperationNumber'] ?? null,
                'auth_result' => $payload['authorizationResult'] ?? null,
            ]);

            // Validate response hash
            if (!$this->alignetService->validateResponse($payload)) {
                throw new Exception('Invalid response signature');
            }

            $authResult = $payload['authorizationResult'] ?? '';
            $isSuccess = $authResult === '00';

            return [
                'success' => true,
                'event_type' => 'payment_response',
                'payment_status' => $isSuccess ? 'completed' : 'failed',
                'data' => [
                    'operation_number' => $payload['purchaseOperationNumber'] ?? null,
                    'authorization_code' => $payload['authorizationCode'] ?? null,
                    'authorization_result' => $authResult,
                    'amount' => isset($payload['purchaseAmount']) ? ($payload['purchaseAmount'] / 100) : null,
                    'currency' => isset($payload['purchaseCurrencyCode']) ? $this->mapCurrencyCode($payload['purchaseCurrencyCode']) : 'USD',
                    'error_code' => $payload['errorCode'] ?? null,
                    'error_message' => $payload['errorMessage'] ?? null,
                    'card_bin' => $payload['bin'] ?? null,
                    'card_brand' => $payload['brand'] ?? null,
                    'payment_reference' => $payload['paymentReferenceCode'] ?? null,
                    'reserved1' => $payload['reserved1'] ?? null,
                    'reserved22' => $payload['reserved22'] ?? null,
                    'reserved23' => $payload['reserved23'] ?? null,
                ],
            ];
        } catch (Exception $e) {
            $this->handleException($e, 'handle_webhook');
        }
    }

    /**
     * Validate credentials
     */
    public function validateCredentials(): bool
    {
        try {
            // Test with a dummy query to see if credentials work
            $testResult = $this->alignetService->queryTransaction('TEST000000001');
            // If we get a response (even if transaction not found), credentials are valid
            return true;
        } catch (Exception $e) {
            $this->logActivity('credential_validation_failed', [
                'error' => $e->getMessage(),
            ], 'error');
            return false;
        }
    }

    /**
     * Alignet supports USD primarily
     */
    public function supportsCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), ['USD']);
    }

    /**
     * Generate unique operation number
     */
    protected function generateOperationNumber(?int $bookingId = null): string
    {
        if ($bookingId) {
            // Use booking ID padded to 9 digits
            return str_pad((string)$bookingId, 9, '0', STR_PAD_LEFT);
        }

        // Generate timestamp-based unique number (9 digits)
        return substr((string)time(), -9);
    }
    /**
     * Map Alignet status to standard status
     */
    protected function mapAlignetStatus(array $result): string
    {
        $authResult = $result['authorizationResult'] ?? '';

        return match ($authResult) {
            '00' => 'completed',
            '' => 'pending',
            default => 'failed',
        };
    }

    /**
     * Map currency code to currency string
     */
    protected function mapCurrencyCode(string $code): string
    {
        return match ($code) {
            '840' => 'USD',
            '188' => 'CRC',
            default => 'USD',
        };
    }

    /**
     * Create customer (Wallet registration)
     */
    public function createCustomer(array $customerData): array
    {
        try {
            $result = $this->alignetService->registerWalletUser(
                $customerData['email'],
                $customerData['first_name'] ?? $customerData['name'] ?? '',
                $customerData['last_name'] ?? ''
            );

            if (!$result || empty($result['token'])) {
                throw new Exception('Failed to register customer in Alignet Wallet');
            }

            $this->logActivity('customer_created', [
                'email' => $customerData['email'],
                'token' => $result['token'],
            ]);

            return [
                'success' => true,
                'gateway_customer_id' => $result['token'],
                'email' => $customerData['email'],
                'code' => $result['code'],
                'description' => $result['description'],
            ];
        } catch (Exception $e) {
            $this->handleException($e, 'create_customer');
        }
    }

    /**
     * Save payment method (not applicable for Alignet - uses Wallet tokens)
     */
    public function savePaymentMethod(string $customerId, string $paymentMethodId): array
    {
        // Alignet uses Wallet tokens which are already saved during customer creation
        return [
            'success' => true,
            'gateway_payment_method_id' => $customerId,
            'type' => 'wallet',
        ];
    }
}
