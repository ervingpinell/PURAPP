<?php

namespace App\Services\PaymentGateway\Exceptions;

class InvalidGatewayResponseException extends \Exception
{
    public function __construct(string $gateway, string $reason)
    {
        parent::__construct("Invalid response from '{$gateway}' gateway: {$reason}");
    }
}
