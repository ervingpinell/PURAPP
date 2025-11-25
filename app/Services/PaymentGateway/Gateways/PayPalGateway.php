<?php

namespace App\Services\PaymentGateway\Gateways;

use App\Services\PaymentGateway\AbstractPaymentGateway;
use App\Services\PaymentGateway\Contracts\PaymentGatewayInterface;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Models\OrderRequest;
use PaypalServerSdkLib\Models\PurchaseUnitRequest;
use PaypalServerSdkLib\Models\AmountWithBreakdown;
use PaypalServerSdkLib\Models\Money;
use PaypalServerSdkLib\Models\OrderApplicationContext;
use PaypalServerSdkLib\Models\RefundRequest;
use PaypalServerSdkLib\Controllers\OrdersController;
use PaypalServerSdkLib\Controllers\PaymentsController;
use PaypalServerSdkLib\Exceptions\ApiException;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use Illuminate\Support\Facades\Log;

class PayPalGateway extends AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected string $gatewayName = 'paypal';
    protected PaypalServerSdkClient $client;
    protected OrdersController $ordersController;
    protected PaymentsController $paymentsController;

    public function __construct(array $config)
    {
        parent::__construct($config);

        Log::info('PayPalGateway: Initializing', [
            'config_keys' => array_keys($config),
            'enabled'     => $config['enabled'] ?? null,
            'mode'        => $config['mode'] ?? null,
        ]);

        // Validar config mínima
        $this->validateConfig(['client_id', 'client_secret', 'mode']);

        // Entorno: sandbox / live
        $environment = $this->config['mode'] === 'live'
            ? Environment::PRODUCTION
            : Environment::SANDBOX;

        Log::info('PayPalGateway: Environment selected', [
            'mode'      => $this->config['mode'],
            'env_const' => $environment,
        ]);

        $this->client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $this->config['client_id'],
                    $this->config['client_secret']
                )
            )
            ->environment($environment)
            ->build();

        $this->ordersController   = $this->client->getOrdersController();
        $this->paymentsController = $this->client->getPaymentsController();

        Log::info('PayPalGateway: Client built successfully');
    }

    public function createPaymentIntent(array $data): \App\Services\PaymentGateway\DTO\PaymentIntentResponse
    {
        $amount   = $data['amount']   ?? 0;
        $currency = $data['currency'] ?? 'USD';
        $options  = $data['options']  ?? [];

        // Validaciones base del AbstractPaymentGateway
        $this->validateAmount($amount);
        $this->validateCurrency($currency);

        Log::info('PayPalGateway:createPaymentIntent: incoming data', [
            'amount'   => $amount,
            'currency' => $currency,
            'options'  => $options,
        ]);

        try {
            // ====== Monto ======
            $amountObj = new AmountWithBreakdown($currency, (string) $amount);

            // ====== Purchase Unit ======
            $purchaseUnit = new PurchaseUnitRequest($amountObj);

            if (!empty($options['description'])) {
                $purchaseUnit->setDescription($options['description']);
            }

            if (!empty($options['reference_id'])) {
                $purchaseUnit->setReferenceId($options['reference_id']);
            }

            // ====== Application Context ======
            $appContext = new OrderApplicationContext();
            $appContext->setBrandName($this->config['brand_name'] ?? 'Green Vacations CR');
            $appContext->setLandingPage($this->config['landing_page'] ?? 'LOGIN');
            $appContext->setUserAction('PAY_NOW');

            if (!empty($options['return_url'])) {
                $appContext->setReturnUrl($options['return_url']);
            }

            if (!empty($options['cancel_url'])) {
                $appContext->setCancelUrl($options['cancel_url']);
            }

            // ====== Order Request ======
            $orderRequest = new OrderRequest('CAPTURE', [$purchaseUnit]);
            $orderRequest->setApplicationContext($appContext);

            Log::info('PayPalGateway:createPaymentIntent: built OrderRequest', [
                'orderRequest' => json_decode(json_encode($orderRequest), true),
            ]);

            // ====== Call API ======
            $response = $this->ordersController->createOrder([
                'body' => $orderRequest,
            ]);

            $result = method_exists($response, 'getResult')
                ? $response->getResult()
                : $response;

            // Normalizar a array SIEMPRE
            $resultArray = is_array($result)
                ? $result
                : json_decode(json_encode($result), true);

            Log::info('PayPalGateway: createPaymentIntent raw result', [
                'raw_type' => gettype($result),
                'keys'     => is_array($resultArray) ? array_keys($resultArray) : null,
                'result'   => $resultArray,
            ]);

            // ====== ID, status, links ======
            $id =
                ($resultArray['id'] ?? null)
                ?? ($resultArray['result']['id'] ?? null)
                ?? ($resultArray['order']['id'] ?? null)
                ?? ($resultArray[0]['id'] ?? null);

            $status =
                ($resultArray['status'] ?? null)
                ?? ($resultArray['result']['status'] ?? null)
                ?? ($resultArray['order']['status'] ?? null)
                ?? ($resultArray[0]['status'] ?? null);

            $links =
                ($resultArray['links'] ?? null)
                ?? ($resultArray['result']['links'] ?? null)
                ?? ($resultArray['order']['links'] ?? null)
                ?? [];

            // 🔑 EXTRAER approval_url
            $approvalUrl = null;
            if (is_array($links)) {
                foreach ($links as $link) {
                    if (($link['rel'] ?? null) === 'approve') {
                        $approvalUrl = $link['href'] ?? null;
                        break;
                    }
                }
            }

            Log::info('PayPalGateway:createPaymentIntent: extracted approval_url', [
                'approval_url' => $approvalUrl,
            ]);

            if (!$id) {
                Log::error('PayPalGateway: Unable to determine order ID from response', [
                    'result' => $resultArray,
                ]);
                throw new \RuntimeException('PayPal did not return a valid order ID');
            }

            // ====== Retornar DTO normalizado ======
            return new \App\Services\PaymentGateway\DTO\PaymentIntentResponse(
                paymentIntentId: $id,
                status: $status ?? 'created',
                clientSecret: $id, // PayPal uses order ID as "secret"
                redirectUrl: $approvalUrl,
                metadata: [
                    'amount' => $amount,
                    'currency' => $currency,
                    'links' => $links,
                ],
                raw: $resultArray
            );
        } catch (ApiException $e) {
            $rawBody    = null;
            $statusCode = null;

            if (method_exists($e, 'getHttpContext') && $e->getHttpContext()) {
                $ctx = $e->getHttpContext();
                if (method_exists($ctx, 'getResponse') && $ctx->getResponse()) {
                    $resp = $ctx->getResponse();
                    if (method_exists($resp, 'getBody')) {
                        $rawBody = (string) $resp->getBody();
                    }
                    if (method_exists($resp, 'getStatusCode')) {
                        $statusCode = $resp->getStatusCode();
                    }
                }
            }

            Log::error('PayPal Create Order Error', [
                'message'     => $e->getMessage(),
                'status_code' => $statusCode,
                'raw_body'    => $rawBody,
                'trace'       => $e->getTraceAsString(),
            ]);

            throw new \Exception('Error creating PayPal order: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('PayPal Create Order - Generic Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function capturePayment(string $paymentIntentId, array $data = []): array
    {
        Log::info('PayPalGateway:capturePayment: starting', [
            'paymentIntentId' => $paymentIntentId,
            'data'            => $data,
        ]);

        try {
            $response = $this->ordersController->captureOrder([
                'id'     => $paymentIntentId,
                'header' => ['Content-Type' => 'application/json'],
            ]);

            $result = method_exists($response, 'getResult')
                ? $response->getResult()
                : $response;

            $resultArray = is_array($result)
                ? $result
                : json_decode(json_encode($result), true);

            Log::info('PayPalGateway:capturePayment raw result', [
                'raw_type' => gettype($result),
                'keys'     => is_array($resultArray) ? array_keys($resultArray) : null,
                'result'   => $resultArray,
            ]);

            $id     = $resultArray['id']     ?? null;
            $status = $resultArray['status'] ?? null;

            $capture     = $resultArray['purchase_units'][0]['payments']['captures'][0] ?? [];
            $amountValue = $capture['amount']['value']         ?? 0;
            $currency    = $capture['amount']['currency_code'] ?? 'USD';

            $normalized = [
                'id'       => $id,
                'status'   => $status,
                'amount'   => (float) $amountValue,
                'currency' => $currency,
                'raw'      => $resultArray,
            ];

            Log::info('PayPalGateway:capturePayment normalized result', $normalized);

            return $normalized;
        } catch (ApiException $e) {
            $rawBody    = null;
            $statusCode = null;

            if (method_exists($e, 'getHttpContext') && $e->getHttpContext()) {
                $ctx = $e->getHttpContext();
                if (method_exists($ctx, 'getResponse') && $ctx->getResponse()) {
                    $resp = $ctx->getResponse();
                    if (method_exists($resp, 'getBody')) {
                        $rawBody = (string) $resp->getBody();
                    }
                    if (method_exists($resp, 'getStatusCode')) {
                        $statusCode = $resp->getStatusCode();
                    }
                }
            }

            Log::error('PayPal Capture Error', [
                'message'     => $e->getMessage(),
                'status_code' => $statusCode,
                'raw_body'    => $rawBody,
                'trace'       => $e->getTraceAsString(),
            ]);

            throw new \Exception('Error capturing PayPal payment: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('PayPal Capture - Generic Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function refundPayment(string $transactionId, float $amount, array $data = []): array
    {
        Log::info('PayPalGateway:refundPayment: starting', [
            'transactionId' => $transactionId,
            'amount'        => $amount,
            'data'          => $data,
        ]);

        try {
            $currency      = $data['currency'] ?? 'USD';
            $money         = new Money($currency, (string) $amount);
            $refundRequest = new RefundRequest();
            $refundRequest->setAmount($money);

            if (isset($data['reason'])) {
                $refundRequest->setNoteToPayer($data['reason']);
            }

            $apiOptions = [
                'capture_id' => $transactionId,
                'body'       => $refundRequest,
            ];

            $response = $this->paymentsController->refundCapturedPayment($apiOptions);

            $result = method_exists($response, 'getResult')
                ? $response->getResult()
                : $response;

            $resultArray = is_array($result)
                ? $result
                : json_decode(json_encode($result), true);

            Log::info('PayPalGateway:refundPayment raw result', [
                'raw_type' => gettype($result),
                'keys'     => is_array($resultArray) ? array_keys($resultArray) : null,
                'result'   => $resultArray,
            ]);

            $id     = $resultArray['id']     ?? null;
            $status = $resultArray['status'] ?? null;

            $normalized = [
                'id'     => $id,
                'status' => $status,
                'raw'    => $resultArray,
            ];

            Log::info('PayPalGateway:refundPayment normalized result', $normalized);

            return $normalized;
        } catch (ApiException $e) {
            $rawBody    = null;
            $statusCode = null;

            if (method_exists($e, 'getHttpContext') && $e->getHttpContext()) {
                $ctx = $e->getHttpContext();
                if (method_exists($ctx, 'getResponse') && $ctx->getResponse()) {
                    $resp = $ctx->getResponse();
                    if (method_exists($resp, 'getBody')) {
                        $rawBody = (string) $resp->getBody();
                    }
                    if (method_exists($resp, 'getStatusCode')) {
                        $statusCode = $resp->getStatusCode();
                    }
                }
            }

            Log::error('PayPal Refund Error', [
                'message'     => $e->getMessage(),
                'status_code' => $statusCode,
                'raw_body'    => $rawBody,
                'trace'       => $e->getTraceAsString(),
            ]);

            throw new \Exception('Error refunding PayPal payment: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('PayPal Refund - Generic Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function getPaymentStatus(string $paymentIntentId): array
    {
        Log::info('PayPalGateway:getPaymentStatus: starting', [
            'paymentIntentId' => $paymentIntentId,
        ]);

        try {
            $response = $this->ordersController->getOrder(['id' => $paymentIntentId]);

            $result = method_exists($response, 'getResult')
                ? $response->getResult()
                : $response;

            $resultArray = is_array($result)
                ? $result
                : json_decode(json_encode($result), true);

            Log::info('PayPalGateway:getPaymentStatus raw result', [
                'raw_type' => gettype($result),
                'keys'     => is_array($resultArray) ? array_keys($resultArray) : null,
                'result'   => $resultArray,
            ]);

            $rawStatus = $resultArray['status'] ?? null;

            $status = match ($rawStatus) {
                'COMPLETED', 'APPROVED' => 'succeeded',
                'PAYER_ACTION_REQUIRED' => 'requires_action',
                'VOIDED'                => 'canceled',
                default                 => 'pending',
            };

            return [
                'status'     => $status,
                'raw_status' => $rawStatus,
                'raw'        => $resultArray,
            ];
        } catch (ApiException $e) {
            $rawBody    = null;
            $statusCode = null;

            if (method_exists($e, 'getHttpContext') && $e->getHttpContext()) {
                $ctx = $e->getHttpContext();
                if (method_exists($ctx, 'getResponse') && $ctx->getResponse()) {
                    $resp = $ctx->getResponse();
                    if (method_exists($resp, 'getBody')) {
                        $rawBody = (string) $resp->getBody();
                    }
                    if (method_exists($resp, 'getStatusCode')) {
                        $statusCode = $resp->getStatusCode();
                    }
                }
            }

            Log::error('PayPal Get Status Error', [
                'message'     => $e->getMessage(),
                'status_code' => $statusCode,
                'raw_body'    => $rawBody,
                'trace'       => $e->getTraceAsString(),
            ]);

            return [
                'status' => 'unknown',
                'error'  => $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            Log::error('PayPal Get Status - Generic Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return [
                'status' => 'unknown',
                'error'  => $e->getMessage(),
            ];
        }
    }

    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        Log::info('PayPal Webhook received', [
            'payload'   => $payload,
            'signature' => $signature,
        ]);

        // TODO: verificación de firma PayPal
        return ['status' => 'processed'];
    }

    public function validateCredentials(): bool
    {
        try {
            // Si llegó al constructor sin explotar, damos OK por ahora
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal validateCredentials error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    public function supportsCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'CRC']);
    }

    public function createCustomer(array $customerData): array
    {
        // No aplicable en este flujo
        return ['id' => null];
    }

    public function savePaymentMethod(string $customerId, string $paymentMethodId): array
    {
        // No implementado para este flujo
        return [];
    }
}
