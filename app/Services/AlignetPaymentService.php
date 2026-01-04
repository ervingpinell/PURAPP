<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SoapClient;
use Exception;

class AlignetPaymentService
{
    protected array $config;
    protected string $environment;

    public function __construct()
    {
        $this->config = config('payment.gateways.alignet');
        $this->environment = $this->config['environment'] ?? 'testing';
    }

    /**
     * Generate SHA-512 hash for purchase verification (sending to VPOS2)
     */
    public function generatePurchaseVerification(
        string $operationNumber,
        string $amount,
        string $currencyCode = '840'
    ): string {
        $string = $this->config['acquirer_id']
            . $this->config['commerce_id']
            . $operationNumber
            . $amount
            . $currencyCode
            . $this->config['secret_key'];

        $hash = hash('sha512', $string);

        Log::info('Alignet Hash Generation', [
            'string_to_hash' => $string,
            'hash' => $hash
        ]);

        return $hash;
    }

    /**
     * Generate SHA-512 hash for response validation
     */
    public function generateResponseVerification(
        string $operationNumber,
        string $amount,
        string $currencyCode,
        string $authorizationResult
    ): string {
        $string = $this->config['acquirer_id']
            . $this->config['commerce_id']
            . $operationNumber
            . $amount
            . $currencyCode
            . $authorizationResult
            . $this->config['secret_key'];

        return hash('sha512', $string);
    }

    /**
     * Generate SHA-512 hash for transaction query
     */
    public function generateQueryVerification(string $operationNumber): string
    {
        $string = $this->config['acquirer_id']
            . $this->config['commerce_id']
            . $operationNumber
            . $this->config['secret_key'];

        return hash('sha512', $string);
    }

    /**
     * Generate SHA-512 hash for Wallet registration
     */
    public function generateWalletVerification(
        string $codCardHolder,
        string $email
    ): string {
        $string = $this->config['wallet_entity_id']
            . $codCardHolder
            . $email
            . $this->config['wallet_secret_key'];

        return hash('sha512', $string);
    }

    /**
     * Prepare payment data for VPOS2
     */
    public function preparePaymentData(
        string $operationNumber,
        float $amount,
        array $customerData,
        ?string $userCodePayme = null
    ): array {
        // Convert amount to cents (safe rounding)
        $amountCents = (string)(int)round($amount * 100);

        $paymentData = [
            'acquirerId' => $this->config['acquirer_id'],
            'idCommerce' => $this->config['commerce_id'],
            'purchaseOperationNumber' => $operationNumber,
            'purchaseAmount' => $amountCents,
            'purchaseCurrencyCode' => '840', // USD
            'language' => 'SP',
            'shippingFirstName' => trim($customerData['first_name'] ?? '') !== '' ? trim($customerData['first_name']) : 'Guest',
            'shippingLastName' => trim($customerData['last_name'] ?? '') !== '' ? trim($customerData['last_name']) : 'User',
            'shippingEmail' => trim($customerData['email'] ?? '') !== '' ? trim($customerData['email']) : 'test@example.com',
            'shippingAddress' => trim($customerData['address'] ?? '') !== '' ? trim($customerData['address']) : 'La Fortuna',
            'shippingZIP' => trim($customerData['zip'] ?? '') !== '' ? trim($customerData['zip']) : '21007',
            'shippingCity' => trim($customerData['city'] ?? '') !== '' ? trim($customerData['city']) : 'Alajuela',
            'shippingState' => trim($customerData['state'] ?? '') !== '' ? trim($customerData['state']) : 'Alajuela',
            'shippingCountry' => trim($customerData['country'] ?? '') !== '' ? trim($customerData['country']) : '188', // 188 = CR numeric
            'userCommerce' => trim($customerData['email'] ?? '') !== '' ? trim($customerData['email']) : 'test@example.com',
            'descriptionProducts' => $customerData['description'] ?? 'Tour booking',
            'programmingLanguage' => 'PHP',
            'purchaseVerification' => $this->generatePurchaseVerification(
                $operationNumber,
                $amountCents
            ),
            // URLs para el formulario (separadas según ejemplo oficial)
            'base_url' => $this->config['urls'][$this->environment]['base'] ?? 'https://integracion.alignetsac.com/',
            'vpos2_script' => $this->config['urls'][$this->environment]['vpos2_script'] ?? 'https://integracion.alignetsac.com/VPOS2/js/modalcomercio.js',
        ];

        // Add optional fields only if set
        if (!empty($userCodePayme)) {
            $paymentData['userCodePayme'] = $userCodePayme;
        }

        if (!empty($customerData['booking_id'])) {
            $paymentData['reserved1'] = (string)$customerData['booking_id'];
        }

        // LOG DETALLADO PARA DEBUG
        Log::info('Alignet Payment Data Prepared', [
            'operation_number' => $operationNumber,
            'amount_original' => $amount,
            'amount_cents' => $amountCents,
            'acquirer_id' => $this->config['acquirer_id'],
            'commerce_id' => $this->config['commerce_id'],
            'currency' => '840',
            'environment' => $this->environment,
            'base_url' => $paymentData['base_url'],
            'vpos2_script' => $paymentData['vpos2_script'],
            'customer_email' => $paymentData['shippingEmail'],
            'verification_string' => $this->config['acquirer_id'] . $this->config['commerce_id'] . $operationNumber . $amountCents . '840',
            'verification_hash' => $paymentData['purchaseVerification'],
        ]);

        return $paymentData;
    }

    /**
     * Validate response from VPOS2
     */
    public function validateResponse(array $responseData): bool
    {
        $receivedHash = $responseData['purchaseVerification'] ?? '';

        if (empty($receivedHash)) {
            Log::warning('Alignet: Empty purchaseVerification in response');
            return false;
        }

        $calculatedHash = $this->generateResponseVerification(
            $responseData['purchaseOperationNumber'] ?? '',
            $responseData['purchaseAmount'] ?? '',
            $responseData['purchaseCurrencyCode'] ?? '',
            $responseData['authorizationResult'] ?? ''
        );

        $isValid = hash_equals($calculatedHash, $receivedHash);

        if (!$isValid) {
            Log::error('Alignet: Hash validation failed', [
                'received' => $receivedHash,
                'calculated' => $calculatedHash,
                'operation_number' => $responseData['purchaseOperationNumber'] ?? null,
            ]);
        }

        return $isValid;
    }

    /**
     * Query transaction status
     */
    public function queryTransaction(string $operationNumber): ?array
    {
        try {
            $verification = $this->generateQueryVerification($operationNumber);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->config['urls'][$this->environment]['query_api'], [
                'idAcquirer' => $this->config['acquirer_id'],
                'idCommerce' => $this->config['commerce_id'],
                'operationNumber' => $operationNumber,
                'purchaseVerification' => $verification
            ]);

            if ($response->successful()) {
                Log::info('Alignet: Transaction query successful', [
                    'operation_number' => $operationNumber,
                    'response' => $response->json()
                ]);
                return $response->json();
            }

            Log::error('Alignet: Transaction query failed', [
                'operation_number' => $operationNumber,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Alignet: Transaction query exception', [
                'operation_number' => $operationNumber,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Register user in Wallet (Pay-me)
     */
    public function registerWalletUser(
        string $email,
        string $firstName,
        string $lastName
    ): ?array {
        try {
            $client = new SoapClient(
                $this->config['urls'][$this->environment]['wallet_wsdl'],
                ['trace' => 1, 'exceptions' => true]
            );

            $codCardHolder = $email;

            $verification = $this->generateWalletVerification($codCardHolder, $email);

            $params = [
                'idEntCommerce' => $this->config['wallet_entity_id'],
                'codCardHolderCommerce' => $codCardHolder,
                'names' => $firstName,
                'lastNames' => $lastName,
                'mail' => $email,
                'reserved1' => '',
                'reserved2' => '',
                'reserved3' => '',
                'registerVerification' => $verification
            ];

            $result = $client->RegisterCardHolder($params);

            Log::info('Alignet Wallet: User registered', [
                'email' => $email,
                'code' => $result->ansCode ?? null,
                'token' => $result->codAsoCardHolderWallet ?? null
            ]);

            return [
                'code' => $result->ansCode ?? null,
                'description' => $result->ansDescription ?? null,
                'token' => $result->codAsoCardHolderWallet ?? null,
                'date' => $result->date ?? null,
                'hour' => $result->hour ?? null
            ];
        } catch (Exception $e) {
            Log::error('Alignet Wallet: Registration error', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get current environment URLs
     */
    public function getUrls(): array
    {
        return $this->config['urls'][$this->environment];
    }

    /**
     * Check if gateway is enabled
     */
    public function isEnabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? false);
    }
}
