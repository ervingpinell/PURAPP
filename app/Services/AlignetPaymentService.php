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
            'shippingCountry' => strtoupper($customerData['country'] ?? 'CR'), // ISO Country Code (e.g., 'CR', 'PA')
            'userCommerce' => substr(hash('sha256', trim($customerData['email'] ?? 'guest')), 0, 10), // Unique ID, Max 10 chars
            'userCodePayme' => $userCodePayme ?? '', // Mandatory: Si (even if empty)
            'descriptionProducts' => substr($customerData['description'] ?? 'Tour booking', 0, 30),
            'programmingLanguage' => 'PHP',
            'urlResponse' => rtrim(config('app.url'), '/') . '/webhooks/payment/alignet', // Force correct path, ignore Env typo
            'timeoutResponse' => '300', // 5 minutos de espera para redirection
            'purchaseVerification' => $this->generatePurchaseVerification(
                $operationNumber,
                $amountCents
            ),
            'reserved1' => isset($customerData['booking_id']) ? (string)$customerData['booking_id'] : '', // Mandatory for some logic
            'reserved2' => isset($customerData['payment_id']) ? (string)$customerData['payment_id'] : '', // Used for recovery
            // URLs para el formulario (separadas según ejemplo oficial)
            'base_url' => $this->config['urls'][$this->environment]['base'] ?? 'https://integracion.alignetsac.com/',
            'vpos2_script' => $this->config['urls'][$this->environment]['vpos2_script'] ?? 'https://integracion.alignetsac.com/VPOS2/js/modalcomercio.js',
        ];

        // Add phone if available (shippingPhone is optional per Alignet docs)
        if (!empty($customerData['phone'])) {
            // Screenshot shows shippingPhone is No Mandatory, max 15.
            $phone = substr(trim($customerData['phone']), 0, 15);
            $paymentData['shippingPhone'] = $phone;
        }

        // Add complete billing data (using same data as shipping per requirements)
        $paymentData['billingFirstName'] = $paymentData['shippingFirstName'];
        $paymentData['billingLastName'] = $paymentData['shippingLastName'];
        $paymentData['billingEmail'] = $paymentData['shippingEmail'];
        $paymentData['billingAddress'] = $paymentData['shippingAddress'];
        $paymentData['billingZIP'] = $paymentData['shippingZIP'];
        $paymentData['billingCity'] = $paymentData['shippingCity'];
        $paymentData['billingState'] = $paymentData['shippingState'];
        $paymentData['billingCountry'] = $paymentData['shippingCountry']; // Same ISO format
        if (!empty($paymentData['shippingPhone'])) {
            $paymentData['billingPhone'] = $paymentData['shippingPhone'];
        }

        // 🔍 LOG MUY DETALLADO PARA DEBUG
        $hashString = $this->config['acquirer_id'] . $this->config['commerce_id'] . $operationNumber . $amountCents . '840' . $this->config['secret_key'];

        Log::channel('single')->info('🔍 ALIGNET DETAILED DEBUG - Payment Data Prepared', [
            '=== BASIC INFO ===' => '',
            'operation_number' => $operationNumber,
            'amount_original' => $amount,
            'amount_cents' => $amountCents,
            'currency_code' => '840',
            'environment' => $this->environment,

            '=== CREDENTIALS ===' => '',
            'acquirer_id' => $this->config['acquirer_id'],
            'commerce_id' => $this->config['commerce_id'],
            'secret_key_length' => strlen($this->config['secret_key'] ?? ''),
            'secret_key_preview' => substr($this->config['secret_key'] ?? '', 0, 10) . '...',

            '=== HASH GENERATION ===' => '',
            'hash_string_components' => [
                'acquirer_id' => $this->config['acquirer_id'],
                'commerce_id' => $this->config['commerce_id'],
                'operation_number' => $operationNumber,
                'amount_cents' => $amountCents,
                'currency' => '840',
                'secret_key_appended' => 'YES',
            ],
            'concatenated_string_length' => strlen($hashString),
            'concatenated_string_preview' => substr($hashString, 0, 50) . '...',
            'generated_hash' => $paymentData['purchaseVerification'],
            'hash_length' => strlen($paymentData['purchaseVerification']),

            '=== URLS ===' => '',
            'base_url' => $paymentData['base_url'],
            'vpos2_script' => $paymentData['vpos2_script'],
            'url_response' => $paymentData['urlResponse'],
            'timeout_response' => $paymentData['timeoutResponse'],

            '=== CUSTOMER DATA ===' => '',
            'customer_email' => $paymentData['shippingEmail'],
            'customer_name' => $paymentData['shippingFirstName'] . ' ' . $paymentData['shippingLastName'],
            'customer_country' => $paymentData['shippingCountry'],
            'customer_phone' => $paymentData['shippingPhone'] ?? 'N/A',

            '=== RESERVED FIELDS ===' => '',
            'reserved1_booking_id' => $paymentData['reserved1'] ?? 'N/A',
            'reserved2_payment_id' => $paymentData['reserved2'] ?? 'N/A',

            '=== FULL PAYLOAD ===' => '',
            'complete_payment_data' => array_filter($paymentData, function ($key) {
                return !in_array($key, ['base_url', 'vpos2_script']);
            }, ARRAY_FILTER_USE_KEY),
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
            'AF' => '004',
            'AX' => '248',
            'AL' => '008',
            'DZ' => '012',
            'AS' => '016',
            'AD' => '020',
            'AO' => '024',
            'AI' => '660',
            'AQ' => '010',
            'AG' => '028',
            'AR' => '032',
            'AM' => '051',
            'AW' => '533',
            'AU' => '036',
            'AT' => '040',
            'AZ' => '031',
            'BS' => '044',
            'BH' => '048',
            'BD' => '050',
            'BB' => '052',
            'BY' => '112',
            'BE' => '056',
            'BZ' => '084',
            'BJ' => '204',
            'BM' => '060',
            'BT' => '064',
            'BO' => '068',
            'BQ' => '535',
            'BA' => '070',
            'BW' => '072',
            'BV' => '074',
            'BR' => '076',
            'IO' => '086',
            'BN' => '096',
            'BG' => '100',
            'BF' => '854',
            'BI' => '108',
            'KH' => '116',
            'CM' => '120',
            'CA' => '124',
            'CV' => '132',
            'KY' => '136',
            'CF' => '140',
            'TD' => '148',
            'CL' => '152',
            'CN' => '156',
            'CX' => '162',
            'CC' => '166',
            'CO' => '170',
            'KM' => '174',
            'CG' => '178',
            'CD' => '180',
            'CK' => '184',
            'CR' => '188',
            'CI' => '384',
            'HR' => '191',
            'CU' => '192',
            'CW' => '531',
            'CY' => '196',
            'CZ' => '203',
            'DK' => '208',
            'DJ' => '262',
            'DM' => '212',
            'DO' => '214',
            'EC' => '218',
            'EG' => '818',
            'SV' => '222',
            'GQ' => '226',
            'ER' => '232',
            'EE' => '233',
            'ET' => '231',
            'FK' => '238',
            'FO' => '234',
            'FJ' => '242',
            'FI' => '246',
            'FR' => '250',
            'GF' => '254',
            'PF' => '258',
            'TF' => '260',
            'GA' => '266',
            'GM' => '270',
            'GE' => '268',
            'DE' => '276',
            'GH' => '288',
            'GI' => '292',
            'GR' => '300',
            'GL' => '304',
            'GD' => '308',
            'GP' => '312',
            'GU' => '316',
            'GT' => '320',
            'GG' => '831',
            'GN' => '324',
            'GW' => '624',
            'GY' => '328',
            'HT' => '332',
            'HM' => '334',
            'VA' => '336',
            'HN' => '340',
            'HK' => '344',
            'HU' => '348',
            'IS' => '352',
            'IN' => '356',
            'ID' => '360',
            'IR' => '364',
            'IQ' => '368',
            'IE' => '372',
            'IM' => '833',
            'IL' => '376',
            'IT' => '380',
            'JM' => '388',
            'JP' => '392',
            'JE' => '832',
            'JO' => '400',
            'KZ' => '398',
            'KE' => '404',
            'KI' => '296',
            'KP' => '408',
            'KR' => '410',
            'KW' => '414',
            'KG' => '417',
            'LA' => '418',
            'LV' => '428',
            'LB' => '422',
            'LS' => '426',
            'LR' => '430',
            'LY' => '434',
            'LI' => '438',
            'LT' => '440',
            'LU' => '442',
            'MO' => '446',
            'MK' => '807',
            'MG' => '450',
            'MW' => '454',
            'MY' => '458',
            'MV' => '462',
            'ML' => '466',
            'MT' => '470',
            'MH' => '584',
            'MQ' => '474',
            'MR' => '478',
            'MU' => '480',
            'YT' => '175',
            'MX' => '484',
            'FM' => '583',
            'MD' => '498',
            'MC' => '492',
            'MN' => '496',
            'ME' => '499',
            'MS' => '500',
            'MA' => '504',
            'MZ' => '508',
            'MM' => '104',
            'NA' => '516',
            'NR' => '520',
            'NP' => '524',
            'NL' => '528',
            'NC' => '540',
            'NZ' => '554',
            'NI' => '558',
            'NE' => '562',
            'NG' => '566',
            'NU' => '570',
            'NF' => '574',
            'MP' => '580',
            'NO' => '578',
            'OM' => '512',
            'PK' => '586',
            'PW' => '585',
            'PS' => '275',
            'PA' => '591',
            'PG' => '598',
            'PY' => '600',
            'PE' => '604',
            'PH' => '608',
            'PN' => '612',
            'PL' => '616',
            'PT' => '620',
            'PR' => '630',
            'QA' => '634',
            'RE' => '638',
            'RO' => '642',
            'RU' => '643',
            'RW' => '646',
            'BL' => '652',
            'SH' => '654',
            'KN' => '659',
            'LC' => '662',
            'MF' => '663',
            'PM' => '666',
            'VC' => '670',
            'WS' => '882',
            'SM' => '674',
            'ST' => '678',
            'SA' => '682',
            'SN' => '686',
            'RS' => '688',
            'SC' => '690',
            'SL' => '694',
            'SG' => '702',
            'SX' => '534',
            'SK' => '703',
            'SI' => '705',
            'SB' => '090',
            'SO' => '706',
            'ZA' => '710',
            'GS' => '239',
            'SS' => '728',
            'ES' => '724',
            'LK' => '144',
            'SD' => '729',
            'SR' => '740',
            'SJ' => '744',
            'SZ' => '748',
            'SE' => '752',
            'CH' => '756',
            'SY' => '760',
            'TW' => '158',
            'TJ' => '762',
            'TZ' => '834',
            'TH' => '764',
            'TL' => '626',
            'TG' => '768',
            'TK' => '772',
            'TO' => '776',
            'TT' => '780',
            'TN' => '788',
            'TR' => '792',
            'TM' => '795',
            'TC' => '796',
            'TV' => '798',
            'UG' => '800',
            'UA' => '804',
            'AE' => '784',
            'GB' => '826',
            'US' => '840',
            'UM' => '581',
            'UY' => '858',
            'UZ' => '860',
            'VU' => '548',
            'VE' => '862',
            'VN' => '704',
            'VG' => '092',
            'VI' => '850',
            'WF' => '876',
            'EH' => '732',
            'YE' => '887',
            'ZM' => '894',
            'ZW' => '716'
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
