<?php

namespace App\Services\PaymentGateway\Exceptions;

/**
 * GatewayNotImplementedException
 *
 * Handles gatewaynotimplementedexception operations.
 */
class GatewayNotImplementedException extends \Exception
{
    public function __construct(string $gateway)
    {
        parent::__construct("Payment gateway '{$gateway}' is not fully implemented yet");
    }
}
