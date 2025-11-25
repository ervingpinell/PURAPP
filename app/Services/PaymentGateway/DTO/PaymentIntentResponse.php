<?php

namespace App\Services\PaymentGateway\DTO;

/**
 * Standardized response for payment intent creation
 * All gateways must return this format
 */
class PaymentIntentResponse
{
    public function __construct(
        public readonly string $paymentIntentId,
        public readonly string $status,
        public readonly ?string $clientSecret = null,
        public readonly ?string $redirectUrl = null,
        public readonly array $metadata = [],
        public readonly array $raw = []
    ) {}

    /**
     * Create from array (for backward compatibility)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            paymentIntentId: $data['payment_intent_id'] ?? $data['id'] ?? throw new \InvalidArgumentException('Missing payment_intent_id'),
            status: $data['status'] ?? 'unknown',
            clientSecret: $data['client_secret'] ?? null,
            redirectUrl: $data['redirect_url'] ?? $data['approval_url'] ?? null,
            metadata: $data['metadata'] ?? [],
            raw: $data['raw'] ?? $data
        );
    }

    /**
     * Convert to array for JSON responses
     */
    public function toArray(): array
    {
        return [
            'success' => true,
            'payment_intent_id' => $this->paymentIntentId,
            'status' => $this->status,
            'client_secret' => $this->clientSecret,
            'redirect_url' => $this->redirectUrl,
            'approval_url' => $this->redirectUrl, // Alias for backward compatibility
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Check if this is a redirect-based gateway (PayPal, etc)
     */
    public function requiresRedirect(): bool
    {
        return !empty($this->redirectUrl);
    }

    /**
     * Check if this is a client-side confirmation gateway (Stripe)
     */
    public function requiresClientConfirmation(): bool
    {
        return !empty($this->clientSecret) && empty($this->redirectUrl);
    }
}
