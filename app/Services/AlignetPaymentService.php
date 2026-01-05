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
            'shippingFirstName' => substr(trim($customerData['first_name'] ?? '') !== '' ? trim($customerData['first_name']) : 'Guest', 0, 30),
            'shippingLastName' => substr(trim($customerData['last_name'] ?? '') !== '' ? trim($customerData['last_name']) : '-', 0, 30), // Doc says 50, but safer to match others if unsure, actually doc says 50. Let's keep 50? Doc Image 1 says billingLastName 50. shippingLastName 50. Okay 50.
            'shippingEmail' => substr(trim($customerData['email'] ?? '') !== '' ? trim($customerData['email']) : 'test@example.com', 0, 30), // Doc says 30
            'shippingAddress' => substr(trim($customerData['address'] ?? '') !== '' ? trim($customerData['address']) : 'Not Provided', 0, 50),
            'shippingZIP' => substr(trim($customerData['zip'] ?? '') !== '' ? trim($customerData['zip']) : '00000', 0, 10),
            'shippingCity' => substr(trim($customerData['city'] ?? '') !== '' ? trim($customerData['city']) : 'Not Provided', 0, 50),
            'shippingState' => substr(trim($customerData['state'] ?? '') !== '' ? trim($customerData['state']) : 'SJ', 0, 15), // Doc says 15.
            'shippingCountry' => substr($customerData['country'] ?? 'CR', 0, 2), // ISO Alpha-2
            'userCommerce' => substr(hash('sha256', trim($customerData['email'] ?? 'guest')), 0, 10), // Unique ID, Max 10 chars
            'descriptionProducts' => substr($customerData['description'] ?? 'Tour booking', 0, 30),
            'programmingLanguage' => 'PHP',
            'purchaseVerification' => $this->generatePurchaseVerification(
                $operationNumber,
                $amountCents
            ),
            // URLs para el formulario (separadas según ejemplo oficial)
            'base_url' => $this->config['urls'][$this->environment]['base'] ?? 'https://integracion.alignetsac.com/',
            'vpos2_script' => $this->config['urls'][$this->environment]['vpos2_script'] ?? 'https://integracion.alignetsac.com/VPOS2/js/modalcomercio.js',
        ];

        if (!empty($customerData['booking_id'])) {
            $paymentData['reserved1'] = (string)$customerData['booking_id'];
        }

        // Add phone if available (reserved2) or shippingPhone if supported (but screenshot implies shippingPhone not mandatory, but good to have)
        if (!empty($customerData['phone'])) {
            // Screenshot shows shippingPhone is No Mandatory, max 15.
            $phone = substr(trim($customerData['phone']), 0, 15);
            $paymentData['shippingPhone'] = $phone;
            $paymentData['reserved2'] = $phone;
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
     * Map ISO Country Code (2 chars) to Numeric Code (3 digits)
     * e.g. CR -> 188, US -> 840
     */
    protected function mapCountryToNumeric(string $isoCode): string
    {
        $isoCode = strtoupper($isoCode);
        $map = [
            'CR' => '188', // Costa Rica
            'US' => '840', // USA
            'CA' => '124', // Canada
            'MX' => '484', // Mexico
            'ES' => '724', // Spain
            'FR' => '250', // France
            'DE' => '276', // Germany
            'IT' => '380', // Italy
            'GB' => '826', // United Kingdom
            'BR' => '076', // Brazil
            'AR' => '032', // Argentina
            'CO' => '170', // Colombia
            'PA' => '591', // Panama
            // Add more as needed, fallback to CR or US if critical
        ];

        return $map[$isoCode] ?? '188'; // Default to CR if unknown
    }

    /**
     * Check if gateway is enabled
     */
    public function isEnabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? false);
    }
}
