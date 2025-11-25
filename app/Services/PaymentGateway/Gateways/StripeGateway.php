<?php

namespace App\Services\PaymentGateway\Gateways;

use App\Services\PaymentGateway\AbstractPaymentGateway;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Webhook;
use Stripe\Exception\ApiErrorException;

class StripeGateway extends AbstractPaymentGateway
{
    protected string $gatewayName = 'stripe';

    public function __construct(array $config)
    {
        parent::__construct($config);

        // Validate required config
        $this->validateConfig(['secret_key', 'publishable_key']);

        // Set Stripe API key
        Stripe::setApiKey($this->config['secret_key']);

        // Set API version if specified
        if (!empty($this->config['api_version'])) {
            Stripe::setApiVersion($this->config['api_version']);
        }
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent(array $data): \App\Services\PaymentGateway\DTO\PaymentIntentResponse
    {
        try {
            $this->validateAmount($data['amount']);
            $this->validateCurrency($data['currency']);

            $this->logActivity('create_payment_intent:incoming', [
                'amount'   => $data['amount'],
                'currency' => $data['currency'],
                'email'    => $data['receipt_email'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
            ]);

            $amount = $this->formatAmountForGateway($data['amount'], $data['currency']);

            $intentData = [
                'amount' => $amount,
                'currency' => strtolower($data['currency']),
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => $this->buildMetadata($data),
            ];

            if (!empty($data['customer_id'])) {
                $intentData['customer'] = $data['customer_id'];
            }

            if (!empty($data['description'])) {
                $intentData['description'] = $data['description'];
            }

            if (!empty($data['receipt_email'])) {
                $intentData['receipt_email'] = $data['receipt_email'];
            }

            $intentData['capture_method'] = $this->config['capture_method'] ?? 'automatic';

            $intent = PaymentIntent::create($intentData);

            $this->logActivity('payment_intent_created', [
                'intent_id' => $intent->id,
                'amount'    => $data['amount'],
                'currency'  => $data['currency'],
            ]);

            return new \App\Services\PaymentGateway\DTO\PaymentIntentResponse(
                paymentIntentId: $intent->id,
                status: $intent->status,
                clientSecret: $intent->client_secret,
                redirectUrl: null, // Stripe doesn't use redirect
                metadata: [
                    'amount' => $this->formatAmountFromGateway($intent->amount, $data['currency']),
                    'currency' => strtoupper($intent->currency),
                ],
                raw: [
                    'id' => $intent->id,
                    'status' => $intent->status,
                    'amount' => $intent->amount,
                    'currency' => $intent->currency,
                ]
            );
        } catch (ApiErrorException $e) {
            $this->handleException($e, 'create_payment_intent');
        }
    }

    public function capturePayment(string $paymentIntentId, array $data = []): array
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);

            if ($intent->capture_method === 'manual' && $intent->status === 'requires_capture') {
                $intent = $intent->capture();
            }

            $this->logActivity('payment_captured', [
                'intent_id' => $intent->id,
                'status'    => $intent->status,
            ]);

            return [
                'success'        => $intent->status === 'succeeded',
                'transaction_id' => $intent->id,
                'status'         => $intent->status,
                'amount'         => $this->formatAmountFromGateway($intent->amount, $intent->currency),
                'currency'       => strtoupper($intent->currency),
                'payment_method' => $this->extractPaymentMethodDetails($intent),
            ];
        } catch (ApiErrorException $e) {
            $this->handleException($e, 'capture_payment');
        }
    }

    public function refundPayment(string $transactionId, float $amount, array $data = []): array
    {
        try {
            $this->validateAmount($amount);

            $intent       = PaymentIntent::retrieve($transactionId);
            $refundAmount = $this->formatAmountForGateway($amount, $intent->currency);

            $refundData = [
                'payment_intent' => $transactionId,
                'amount'         => $refundAmount,
            ];

            if (!empty($data['reason'])) {
                $refundData['reason'] = $data['reason'];
            }

            if (!empty($data['metadata'])) {
                $refundData['metadata'] = $data['metadata'];
            }

            $refund = Refund::create($refundData);

            $this->logActivity('payment_refunded', [
                'refund_id'         => $refund->id,
                'payment_intent_id' => $transactionId,
                'amount'            => $amount,
            ]);

            return [
                'success'  => true,
                'refund_id' => $refund->id,
                'status'   => $refund->status,
                'amount'   => $this->formatAmountFromGateway($refund->amount, $intent->currency),
                'currency' => strtoupper($intent->currency),
            ];
        } catch (ApiErrorException $e) {
            $this->handleException($e, 'refund_payment');
        }
    }

    public function getPaymentStatus(string $paymentIntentId): array
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'success'        => true,
                'status'         => $intent->status,
                'amount'         => $this->formatAmountFromGateway($intent->amount, $intent->currency),
                'currency'       => strtoupper($intent->currency),
                'payment_method' => $this->extractPaymentMethodDetails($intent),
            ];
        } catch (ApiErrorException $e) {
            $this->handleException($e, 'get_payment_status');
        }
    }

    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        try {
            if (!empty($this->config['webhook_secret']) && $signature) {
                $event = Webhook::constructEvent(
                    json_encode($payload),
                    $signature,
                    $this->config['webhook_secret']
                );
            } else {
                $event = (object) $payload;
            }

            $this->logActivity('webhook_received', [
                'type' => $event->type ?? 'unknown',
                'id'   => $event->id ?? null,
            ]);

            return [
                'success'    => true,
                'event_type' => $event->type ?? 'unknown',
                'event_id'   => $event->id ?? null,
                'data'       => $event->data ?? null,
            ];
        } catch (\Exception $e) {
            $this->handleException($e, 'handle_webhook');
        }
    }

    public function validateCredentials(): bool
    {
        try {
            $account = \Stripe\Account::retrieve();
            return !empty($account->id);
        } catch (ApiErrorException $e) {
            $this->logActivity('credential_validation_failed', [
                'error' => $e->getMessage(),
            ], 'error');
            return false;
        }
    }

    public function supportsCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), ['USD', 'CRC']);
    }

    public function createCustomer(array $customerData): array
    {
        try {
            $data = [];

            if (!empty($customerData['email'])) {
                $data['email'] = $customerData['email'];
            }

            if (!empty($customerData['name'])) {
                $data['name'] = $customerData['name'];
            }

            if (!empty($customerData['phone'])) {
                $data['phone'] = $customerData['phone'];
            }

            if (!empty($customerData['metadata'])) {
                $data['metadata'] = $customerData['metadata'];
            }

            $customer = Customer::create($data);

            $this->logActivity('customer_created', [
                'customer_id' => $customer->id,
                'email'       => $customer->email,
            ]);

            return [
                'success'             => true,
                'gateway_customer_id' => $customer->id,
                'email'               => $customer->email,
                'name'                => $customer->name,
            ];
        } catch (ApiErrorException $e) {
            $this->handleException($e, 'create_customer');
        }
    }

    public function savePaymentMethod(string $customerId, string $paymentMethodId): array
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customerId]);

            $this->logActivity('payment_method_saved', [
                'customer_id'       => $customerId,
                'payment_method_id' => $paymentMethodId,
            ]);

            return [
                'success'                 => true,
                'gateway_payment_method_id' => $paymentMethod->id,
                'type'                    => $paymentMethod->type,
                'card'                    => $paymentMethod->card ? [
                    'brand'       => $paymentMethod->card->brand,
                    'last4'       => $paymentMethod->card->last4,
                    'exp_month'   => $paymentMethod->card->exp_month,
                    'exp_year'    => $paymentMethod->card->exp_year,
                    'fingerprint' => $paymentMethod->card->fingerprint,
                ] : null,
            ];
        } catch (ApiErrorException $e) {
            $this->handleException($e, 'save_payment_method');
        }
    }

    protected function extractPaymentMethodDetails(PaymentIntent $intent): ?array
    {
        if (!$intent->payment_method) {
            return null;
        }

        try {
            $pm = is_string($intent->payment_method)
                ? PaymentMethod::retrieve($intent->payment_method)
                : $intent->payment_method;

            return [
                'type'       => $pm->type ?? null,
                'card_brand' => $pm->card->brand ?? null,
                'card_last4' => $pm->card->last4 ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
