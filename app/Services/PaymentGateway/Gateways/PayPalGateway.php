<?php

namespace App\Services\PaymentGateway\Gateways;

use App\Services\PaymentGateway\AbstractPaymentGateway;
use PaypalServerSDKLib\PaypalServerSDKClient;
use PaypalServerSDKLib\PaypalServerSDKClientBuilder;
use PaypalServerSDKLib\Models\OrderRequest;
use PaypalServerSDKLib\Models\PurchaseUnitRequest;
use PaypalServerSDKLib\Models\AmountWithBreakdown;
use PaypalServerSDKLib\Models\Money;
use PaypalServerSDKLib\Controllers\OrdersController;
use PaypalServerSDKLib\Controllers\PaymentsController;
use PaypalServerSDKLib\Exceptions\ApiException;
use PaypalServerSDKLib\Authentication\ClientCredentialsAuthCredentials;
use PaypalServerSDKLib\Environment;

class PayPalGateway extends AbstractPaymentGateway
{
    protected string $gatewayName = 'paypal';
    protected PaypalServerSDKClient $client;
    protected OrdersController $ordersController;
    protected PaymentsController $paymentsController;

    public function __construct(array $config)
    {
        parent::__construct($config);

        // Validate required config
        $this->validateConfig(['client_id', 'client_secret', 'mode']);

        // Initialize PayPal client
        $environment = $this->config['mode'] === 'live'
            ? Environment::PRODUCTION
            : Environment::SANDBOX;

        $this->client = PaypalServerSDKClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                new ClientCredentialsAuthCredentials(
                    $this->config['client_id'],
                    $this->config['client_secret']
                )
            )
            ->environment($environment)
            ->build();

        $this->ordersController = $this->client->getOrdersController();
        $this->paymentsController = $this->client->getPaymentsController();
    }

    /**
     * Create a PayPal Order (equivalent to payment intent)
     */
    public function createPaymentIntent(array $data): array
    {
        try {
            $this->validateAmount($data['amount']);
            $this->validateCurrency($data['currency']);

            // Build order request
            $orderRequest = new OrderRequest();
            $orderRequest->intent = 'CAPTURE';

            // Create purchase unit
            $purchaseUnit = new PurchaseUnitRequest();
            $purchaseUnit->referenceId = $data['booking_reference'] ?? 'booking_' . time();
            $purchaseUnit->description = $data['description'] ?? 'Tour Booking';
            $purchaseUnit->customId = $data['payment_id'] ?? null;

            // Set amount
            $amount = new AmountWithBreakdown();
            $amount->currencyCode = strtoupper($data['currency']);
            $amount->value = number_format($data['amount'], 2, '.', '');
            $purchaseUnit->amount = $amount;

            $orderRequest->purchaseUnits = [$purchaseUnit];

            // Set application context (return URLs, etc.)
            if (!empty($data['return_url']) || !empty($data['cancel_url'])) {
                $appContext = new \PaypalServerSDKLib\Models\OrderApplicationContext();
                $appContext->returnUrl = $data['return_url'] ?? url('/payment/success');
                $appContext->cancelUrl = $data['cancel_url'] ?? url('/payment/cancel');
                $appContext->brandName = $this->config['brand_name'] ?? config('app.name');
                $appContext->landingPage = $this->config['landing_page'] ?? 'LOGIN';
                $appContext->userAction = 'PAY_NOW';
                $orderRequest->applicationContext = $appContext;
            }

            // Create order
            $response = $this->ordersController->ordersCreate($orderRequest);
            $order = $response->getResult();

            $this->logActivity('order_created', [
                'order_id' => $order->id,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
            ]);

            // Get approval URL
            $approvalUrl = null;
            if ($order->links) {
                foreach ($order->links as $link) {
                    if ($link->rel === 'approve') {
                        $approvalUrl = $link->href;
                        break;
                    }
                }
            }

            return [
                'success' => true,
                'payment_intent_id' => $order->id,
                'client_secret' => null, // PayPal doesn't use client secrets
                'approval_url' => $approvalUrl,
                'status' => strtolower($order->status),
                'amount' => $data['amount'],
                'currency' => strtoupper($data['currency']),
            ];
        } catch (ApiException $e) {
            $this->handleException($e, 'create_payment_intent');
        } catch (\Exception $e) {
            $this->handleException($e, 'create_payment_intent');
        }
    }

    /**
     * Capture a PayPal Order
     */
    public function capturePayment(string $paymentIntentId, array $data = []): array
    {
        try {
            $response = $this->ordersController->ordersCapture($paymentIntentId);
            $order = $response->getResult();

            $this->logActivity('payment_captured', [
                'order_id' => $order->id,
                'status' => $order->status,
            ]);

            // Extract payment method details
            $paymentMethod = $this->extractPaymentMethodDetails($order);

            return [
                'success' => $order->status === 'COMPLETED',
                'transaction_id' => $order->id,
                'status' => strtolower($order->status),
                'amount' => $this->getOrderAmount($order),
                'currency' => $this->getOrderCurrency($order),
                'payment_method' => $paymentMethod,
            ];
        } catch (ApiException $e) {
            $this->handleException($e, 'capture_payment');
        } catch (\Exception $e) {
            $this->handleException($e, 'capture_payment');
        }
    }

    /**
     * Refund a PayPal payment
     */
    public function refundPayment(string $transactionId, float $amount, array $data = []): array
    {
        try {
            $this->validateAmount($amount);

            // Get the order to find the capture ID
            $orderResponse = $this->ordersController->ordersGet($transactionId);
            $order = $orderResponse->getResult();

            // Find the capture ID from purchase units
            $captureId = null;
            if ($order->purchaseUnits && count($order->purchaseUnits) > 0) {
                $purchaseUnit = $order->purchaseUnits[0];
                if ($purchaseUnit->payments && $purchaseUnit->payments->captures && count($purchaseUnit->payments->captures) > 0) {
                    $captureId = $purchaseUnit->payments->captures[0]->id;
                }
            }

            if (!$captureId) {
                throw new \Exception('No capture found for this order');
            }

            // Create refund request
            $refundRequest = new \PaypalServerSDKLib\Models\RefundRequest();
            $refundAmount = new Money();
            $refundAmount->currencyCode = $this->getOrderCurrency($order);
            $refundAmount->value = number_format($amount, 2, '.', '');
            $refundRequest->amount = $refundAmount;

            if (!empty($data['note'])) {
                $refundRequest->noteToPayer = $data['note'];
            }

            // Process refund
            $response = $this->paymentsController->capturesRefund($captureId, $refundRequest);
            $refund = $response->getResult();

            $this->logActivity('payment_refunded', [
                'refund_id' => $refund->id,
                'capture_id' => $captureId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => strtolower($refund->status),
                'amount' => $amount,
                'currency' => $refundAmount->currencyCode,
            ];
        } catch (ApiException $e) {
            $this->handleException($e, 'refund_payment');
        } catch (\Exception $e) {
            $this->handleException($e, 'refund_payment');
        }
    }

    /**
     * Get PayPal Order status
     */
    public function getPaymentStatus(string $paymentIntentId): array
    {
        try {
            $response = $this->ordersController->ordersGet($paymentIntentId);
            $order = $response->getResult();

            $paymentMethod = $this->extractPaymentMethodDetails($order);

            return [
                'success' => true,
                'status' => strtolower($order->status),
                'amount' => $this->getOrderAmount($order),
                'currency' => $this->getOrderCurrency($order),
                'payment_method' => $paymentMethod,
            ];
        } catch (ApiException $e) {
            $this->handleException($e, 'get_payment_status');
        } catch (\Exception $e) {
            $this->handleException($e, 'get_payment_status');
        }
    }

    /**
     * Handle PayPal webhook
     */
    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        try {
            // PayPal webhook verification would go here
            // For now, we'll just log and return the event

            $eventType = $payload['event_type'] ?? 'unknown';

            $this->logActivity('webhook_received', [
                'event_type' => $eventType,
                'resource_type' => $payload['resource_type'] ?? null,
            ]);

            return [
                'success' => true,
                'event_type' => $eventType,
                'event_id' => $payload['id'] ?? null,
                'data' => $payload['resource'] ?? null,
            ];
        } catch (\Exception $e) {
            $this->handleException($e, 'handle_webhook');
        }
    }

    /**
     * Validate PayPal credentials
     */
    public function validateCredentials(): bool
    {
        try {
            // Try to create a minimal order to test credentials
            $testData = [
                'amount' => 1.00,
                'currency' => 'USD',
                'description' => 'Credential validation test',
            ];

            $result = $this->createPaymentIntent($testData);
            return $result['success'] ?? false;
        } catch (\Exception $e) {
            $this->logActivity('credential_validation_failed', [
                'error' => $e->getMessage(),
            ], 'error');
            return false;
        }
    }

    /**
     * PayPal supports many currencies
     */
    public function supportsCurrency(string $currency): bool
    {
        $supported = [
            'USD',
            'EUR',
            'GBP',
            'CAD',
            'AUD',
            'JPY',
            'CNY',
            'CHF',
            'SEK',
            'NZD',
            'MXN',
            'SGD',
            'HKD',
            'NOK',
            'DKK',
            'PLN',
            'CZK',
            'HUF',
            'ILS',
            'BRL',
            'MYR',
            'PHP',
            'TWD',
            'THB',
            'CRC', // Costa Rican Colón
        ];

        return in_array(strtoupper($currency), $supported);
    }

    /**
     * Create PayPal customer (not commonly used in PayPal Orders API)
     */
    public function createCustomer(array $customerData): array
    {
        // PayPal Orders API doesn't require pre-creating customers
        // Customer info is passed with each order
        return [
            'success' => true,
            'gateway_customer_id' => null,
            'message' => 'PayPal does not require customer pre-creation',
        ];
    }

    /**
     * Save payment method (not commonly used in PayPal Orders API)
     */
    public function savePaymentMethod(string $customerId, string $paymentMethodId): array
    {
        // PayPal Orders API doesn't use saved payment methods in the same way
        // This would require PayPal Vault API
        return [
            'success' => true,
            'gateway_payment_method_id' => null,
            'message' => 'PayPal payment method saving requires Vault API',
        ];
    }

    /**
     * Extract payment method details from order
     */
    protected function extractPaymentMethodDetails($order): ?array
    {
        if (!$order->purchaseUnits || count($order->purchaseUnits) === 0) {
            return null;
        }

        $purchaseUnit = $order->purchaseUnits[0];
        if (!$purchaseUnit->payments) {
            return null;
        }

        $payments = $purchaseUnit->payments;

        // Check for card payment
        if ($payments->captures && count($payments->captures) > 0) {
            $capture = $payments->captures[0];

            if (isset($capture->paymentSource)) {
                $paymentSource = $capture->paymentSource;

                // Card payment
                if (isset($paymentSource->card)) {
                    return [
                        'type' => 'card',
                        'card_brand' => strtolower($paymentSource->card->brand ?? 'unknown'),
                        'card_last4' => $paymentSource->card->lastDigits ?? null,
                    ];
                }

                // PayPal account
                if (isset($paymentSource->paypal)) {
                    return [
                        'type' => 'paypal',
                        'card_brand' => null,
                        'card_last4' => null,
                    ];
                }
            }
        }

        return [
            'type' => 'paypal',
            'card_brand' => null,
            'card_last4' => null,
        ];
    }

    /**
     * Get order amount
     */
    protected function getOrderAmount($order): float
    {
        if ($order->purchaseUnits && count($order->purchaseUnits) > 0) {
            $amount = $order->purchaseUnits[0]->amount;
            return (float) $amount->value;
        }
        return 0.0;
    }

    /**
     * Get order currency
     */
    protected function getOrderCurrency($order): string
    {
        if ($order->purchaseUnits && count($order->purchaseUnits) > 0) {
            $amount = $order->purchaseUnits[0]->amount;
            return strtoupper($amount->currencyCode);
        }
        return 'USD';
    }
}
