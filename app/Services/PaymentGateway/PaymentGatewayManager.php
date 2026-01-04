<?php

namespace App\Services\PaymentGateway;

use App\Services\PaymentGateway\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateway\Gateways\StripeGateway;
use App\Services\PaymentGateway\Gateways\PayPalGateway;


class PaymentGatewayManager
{
    protected array $gateways = [];
    protected ?string $defaultGateway = null;

    public function __construct()
    {
        $this->defaultGateway = config('payment.default_gateway', 'stripe');
    }

    /**
     * Get a payment gateway instance
     *
     * @param string|null $gateway Gateway name (stripe, tilopay, etc.)
     * @return PaymentGatewayInterface
     * @throws \Exception
     */
    public function driver(?string $gateway = null): PaymentGatewayInterface
    {
        $gateway = $gateway ?? $this->defaultGateway;

        // Return cached instance if exists
        if (isset($this->gateways[$gateway])) {
            return $this->gateways[$gateway];
        }

        // Create new instance
        $this->gateways[$gateway] = $this->createGateway($gateway);

        return $this->gateways[$gateway];
    }

    /**
     * Create a gateway instance
     *
     * @param string $gateway
     * @return PaymentGatewayInterface
     * @throws \App\Services\PaymentGateway\Exceptions\GatewayNotEnabledException
     * @throws \App\Services\PaymentGateway\Exceptions\GatewayNotImplementedException
     * @throws \Exception
     */
    protected function createGateway(string $gateway): PaymentGatewayInterface
    {
        $config = config("payment.gateways.{$gateway}");

        if (!$config) {
            throw new \Exception("Payment gateway configuration not found: {$gateway}");
        }

        // Check Settings table for dynamic configuration first
        $settingKey = "payment.gateway.{$gateway}";
        $settingEnabled = \App\Models\Setting::where('key', $settingKey)->value('value');

        // Check config as fallback
        $configEnabled = $config['enabled'] ?? false;

        // Determine final enabled status
        $isEnabled = false;

        if ($settingEnabled !== null) {
            // If setting exists, it overrides config
            $isEnabled = filter_var($settingEnabled, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool)$settingEnabled;
        } else {
            // Fallback to config if no setting
            $isEnabled = $configEnabled;
        }

        if (!$isEnabled) {
            throw new \App\Services\PaymentGateway\Exceptions\GatewayNotEnabledException($gateway);
        }

        return match ($gateway) {
            'stripe' => new StripeGateway($config),
            'paypal' => new PayPalGateway($config),
            'alignet' => new \App\Services\PaymentGateway\Gateways\AlignetGateway($config),
            default => throw new \Exception("Unknown payment gateway: {$gateway}"),
        };
    }

    /**
     * Get all enabled gateways
     *
     * @return array
     */
    public function getEnabledGateways(): array
    {
        $gateways = config('payment.gateways', []);
        $enabled = [];

        foreach ($gateways as $name => $config) {
            if ($config['enabled'] ?? false) {
                $enabled[] = $name;
            }
        }

        return $enabled;
    }

    /**
     * Check if a gateway is enabled
     *
     * @param string $gateway
     * @return bool
     */
    public function isGatewayEnabled(string $gateway): bool
    {
        return (bool) config("payment.gateways.{$gateway}.enabled", false);
    }

    /**
     * Get default gateway
     *
     * @return string
     */
    public function getDefaultGateway(): string
    {
        return $this->defaultGateway;
    }

    /**
     * Set default gateway
     *
     * @param string $gateway
     * @return self
     */
    public function setDefaultGateway(string $gateway): self
    {
        $this->defaultGateway = $gateway;
        return $this;
    }

    /**
     * Get gateway for a specific currency
     *
     * @param string $currency
     * @return PaymentGatewayInterface
     * @throws \Exception
     */
    public function getGatewayForCurrency(string $currency): PaymentGatewayInterface
    {
        $enabledGateways = $this->getEnabledGateways();

        foreach ($enabledGateways as $gatewayName) {
            try {
                $gateway = $this->driver($gatewayName);
                if ($gateway->supportsCurrency($currency)) {
                    return $gateway;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new \Exception("No enabled gateway supports currency: {$currency}");
    }

    /**
     * Validate all gateway configurations
     *
     * @return array Results of validation for each gateway
     */
    public function validateAllGateways(): array
    {
        $results = [];
        $gateways = config('payment.gateways', []);

        foreach ($gateways as $name => $config) {
            if (!($config['enabled'] ?? false)) {
                $results[$name] = [
                    'enabled' => false,
                    'valid' => null,
                    'message' => 'Gateway is disabled',
                ];
                continue;
            }

            try {
                $gateway = $this->driver($name);
                $isValid = $gateway->validateCredentials();

                $results[$name] = [
                    'enabled' => true,
                    'valid' => $isValid,
                    'message' => $isValid ? 'Credentials are valid' : 'Credentials validation failed',
                ];
            } catch (\Exception $e) {
                $results[$name] = [
                    'enabled' => true,
                    'valid' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
