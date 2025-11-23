<?php

namespace App\Services\PaymentGateway;

use App\Services\PaymentGateway\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected array $config;
    protected string $gatewayName;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get gateway name
     */
    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    /**
     * Format amount for gateway (handle cents vs whole units)
     *
     * @param float $amount Amount in dollars/colones
     * @param string $currency Currency code
     * @return int Amount in gateway's expected format
     */
    protected function formatAmountForGateway(float $amount, string $currency): int
    {
        $divisor = config("payment.currencies.{$currency}.stripe_divisor", 100);
        return (int) round($amount * $divisor);
    }

    /**
     * Format amount from gateway to decimal
     *
     * @param int $amount Amount from gateway
     * @param string $currency Currency code
     * @return float Amount in decimal format
     */
    protected function formatAmountFromGateway(int $amount, string $currency): float
    {
        $divisor = config("payment.currencies.{$currency}.stripe_divisor", 100);
        return round($amount / $divisor, 2);
    }

    /**
     * Validate required configuration keys
     *
     * @param array $requiredKeys
     * @throws \Exception
     */
    protected function validateConfig(array $requiredKeys): void
    {
        foreach ($requiredKeys as $key) {
            if (empty($this->config[$key])) {
                throw new \Exception("Missing required configuration: {$key} for {$this->gatewayName} gateway");
            }
        }
    }

    /**
     * Log gateway activity
     *
     * @param string $action Action being performed
     * @param array $data Additional data to log
     * @param string $level Log level (info, error, warning)
     */
    protected function logActivity(string $action, array $data = [], string $level = 'info'): void
    {
        $logData = array_merge([
            'gateway' => $this->gatewayName,
            'action' => $action,
        ], $data);

        Log::channel('daily')->{$level}("[Payment Gateway] {$this->gatewayName}: {$action}", $logData);
    }

    /**
     * Handle gateway exception and format error
     *
     * @param \Exception $e
     * @param string $action
     * @throws \Exception
     */
    protected function handleException(\Exception $e, string $action): void
    {
        $this->logActivity($action, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 'error');

        throw new \Exception(
            "Payment gateway error ({$this->gatewayName}): {$e->getMessage()}",
            $e->getCode(),
            $e
        );
    }

    /**
     * Build metadata for payment
     *
     * @param array $data
     * @return array
     */
    protected function buildMetadata(array $data): array
    {
        return [
            'booking_id' => $data['booking_id'] ?? null,
            'booking_reference' => $data['booking_reference'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'user_email' => $data['user_email'] ?? null,
            'tour_name' => $data['tour_name'] ?? null,
            'tour_date' => $data['tour_date'] ?? null,
            'environment' => app()->environment(),
        ];
    }

    /**
     * Check if gateway is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? false);
    }

    /**
     * Default currency support (override in specific gateways)
     *
     * @param string $currency
     * @return bool
     */
    public function supportsCurrency(string $currency): bool
    {
        // By default, support USD
        return in_array(strtoupper($currency), ['USD']);
    }

    /**
     * Validate amount
     *
     * @param float $amount
     * @throws \Exception
     */
    protected function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \Exception('Payment amount must be greater than zero');
        }

        // Maximum amount check (adjust as needed)
        if ($amount > 999999.99) {
            throw new \Exception('Payment amount exceeds maximum allowed');
        }
    }

    /**
     * Validate currency
     *
     * @param string $currency
     * @throws \Exception
     */
    protected function validateCurrency(string $currency): void
    {
        if (!$this->supportsCurrency($currency)) {
            throw new \Exception("Currency {$currency} is not supported by {$this->gatewayName}");
        }
    }
}
