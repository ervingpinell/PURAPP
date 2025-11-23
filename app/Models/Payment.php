<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'booking_id',
        'user_id',
        'gateway',
        'gateway_transaction_id',
        'gateway_payment_intent_id',
        'amount',
        'currency',
        'amount_refunded',
        'status',
        'bookings_created',
        'bookings_created_at',
        'payment_method_type',
        'card_brand',
        'card_last4',
        'gateway_response',
        'metadata',
        'error_code',
        'error_message',
        'paid_at',
        'refunded_at',
        'failed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_refunded' => 'decimal:2',
        'bookings_created' => 'boolean',
        'bookings_created_at' => 'datetime',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ==========================================
       Relationships
       ========================================== */

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /* ==========================================
       Scopes
       ========================================== */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'cancelled']);
    }

    public function scopeRefunded($query)
    {
        return $query->whereIn('status', ['refunded', 'partially_refunded']);
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /* ==========================================
       Accessors & Mutators
       ========================================== */

    public function getFormattedAmountAttribute(): string
    {
        $symbol = config("payment.currencies.{$this->currency}.symbol", '$');
        return $symbol . number_format((float) $this->amount, 2);
    }

    public function getFormattedAmountRefundedAttribute(): string
    {
        $symbol = config("payment.currencies.{$this->currency}.symbol", '$');
        return $symbol . number_format((float) $this->amount_refunded, 2);
    }

    public function getNetAmountAttribute(): float
    {
        return round($this->amount - $this->amount_refunded, 2);
    }

    public function getIsRefundableAttribute(): bool
    {
        return $this->status === 'completed'
            && $this->amount_refunded < $this->amount;
    }

    public function getIsFullyRefundedAttribute(): bool
    {
        return $this->status === 'refunded'
            || ($this->amount_refunded >= $this->amount && $this->amount_refunded > 0);
    }

    public function getIsPartiallyRefundedAttribute(): bool
    {
        return $this->amount_refunded > 0 && $this->amount_refunded < $this->amount;
    }

    /* ==========================================
       Helper Methods
       ========================================== */

    /**
     * Mark payment as completed
     */
    public function markAsCompleted(array $gatewayResponse = []): bool
    {
        return $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $errorCode = null, string $errorMessage = null): bool
    {
        return $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Record a refund
     */
    public function recordRefund(float $amount, array $gatewayResponse = []): bool
    {
        $newRefundedAmount = $this->amount_refunded + $amount;
        $isFullRefund = $newRefundedAmount >= $this->amount;

        return $this->update([
            'amount_refunded' => $newRefundedAmount,
            'status' => $isFullRefund ? 'refunded' : 'partially_refunded',
            'refunded_at' => $isFullRefund ? now() : $this->refunded_at,
            'gateway_response' => array_merge($this->gateway_response ?? [], [
                'refunds' => array_merge($this->gateway_response['refunds'] ?? [], [$gatewayResponse])
            ]),
        ]);
    }

    /**
     * Get payment status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'success',
            'pending', 'processing' => 'warning',
            'failed', 'cancelled' => 'danger',
            'refunded', 'partially_refunded' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get payment status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => __('Pending'),
            'processing' => __('Processing'),
            'completed' => __('Completed'),
            'failed' => __('Failed'),
            'cancelled' => __('Cancelled'),
            'refunded' => __('Refunded'),
            'partially_refunded' => __('Partially Refunded'),
            default => ucfirst($this->status),
        };
    }
}
