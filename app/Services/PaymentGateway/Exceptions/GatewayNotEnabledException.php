<?php

namespace App\Services\PaymentGateway\Exceptions;

/**
 * GatewayNotEnabledException
 *
 * Handles gatewaynotenabledexception operations.
 */
class GatewayNotEnabledException extends \Exception
{
    public function __construct(string $gateway)
    {
        parent::__construct("Payment gateway '{$gateway}' is not enabled");
    }
}
