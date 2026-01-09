<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment Model
 *
 * Represents a payment transaction for a booking.
 * Handles payment lifecycle, refunds, and gateway interactions.
 *
 * @property int $payment_id Primary key
 * @property int $booking_id Associated booking
 * @property int|null $user_id User who made the payment
 * @property string $gateway Payment gateway used (alignet, stripe, paypal, etc.)
 * @property string|null $gateway_transaction_id Transaction ID from gateway
 * @property string|null $gateway_payment_intent_id Payment intent ID from gateway
 * @property float $amount Payment amount
 * @property string $currency Currency code (USD, CRC, etc.)
 * @property float $amount_refunded Amount refunded
 * @property string $status Payment status (pending, completed, failed, refunded, etc.)
 * @property bool $bookings_created Whether bookings were created from this payment
 * @property \Carbon\Carbon|null $bookings_created_at When bookings were created
 * @property string|null $payment_method_type Payment method type (card, wallet, etc.)
 * @property string|null $card_brand Card brand (visa, mastercard, etc.)
 * @property string|null $card_last4 Last 4 digits of card
 * @property array|null $gateway_response Raw gateway response data
 * @property array|null $metadata Additional payment metadata
 * @property string|null $error_code Error code if payment failed
 * @property string|null $error_message Error message if payment failed
 * @property \Carbon\Carbon|null $paid_at When payment was completed
 * @property \Carbon\Carbon|null $refunded_at When payment was refunded
 * @property \Carbon\Carbon|null $failed_at When payment failed
 *
 * @property-read Booking $booking
 * @property-read User|null $user
 * @property-read string $formatted_amount
 * @property-read string $formatted_amount_refunded
 * @property-read float $net_amount
 * @property-read bool $is_refundable
 * @property-read bool $is_fully_refunded
 * @property-read bool $is_partially_refunded
 * @property-read string $status_color
 * @property-read string $status_label
 */
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

    /**
     * Booking this payment is for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * User who made the payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /* ==========================================
       Scopes
       ========================================== */

    /**
     * Scope to pending payments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to completed payments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to failed/cancelled payments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'cancelled']);
    }

    /**
     * Scope to refunded payments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRefunded($query)
    {
        return $query->whereIn('status', ['refunded', 'partially_refunded']);
    }

    /**
     * Scope to payments by gateway.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $gateway Gateway name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /* ==========================================
       Accessors & Mutators
       ========================================== */

    /**
     * Get formatted amount with currency symbol.
     *
     * @return string
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbol = config("payment.currencies.{$this->currency}.symbol", '$');
        return $symbol . number_format((float) $this->amount, 2);
    }

    /**
     * Get formatted refunded amount with currency symbol.
     *
     * @return string
     */
    public function getFormattedAmountRefundedAttribute(): string
    {
        $symbol = config("payment.currencies.{$this->currency}.symbol", '$');
        return $symbol . number_format((float) $this->amount_refunded, 2);
    }

    /**
     * Get net amount after refunds.
     *
     * @return float
     */
    public function getNetAmountAttribute(): float
    {
        return round($this->amount - $this->amount_refunded, 2);
    }

    /**
     * Check if payment can be refunded.
     *
     * @return bool
     */
    public function getIsRefundableAttribute(): bool
    {
        return $this->status === 'completed'
            && $this->amount_refunded < $this->amount;
    }

    /**
     * Check if payment is fully refunded.
     *
     * @return bool
     */
    public function getIsFullyRefundedAttribute(): bool
    {
        return $this->status === 'refunded'
            || ($this->amount_refunded >= $this->amount && $this->amount_refunded > 0);
    }

    /**
     * Check if payment is partially refunded.
     *
     * @return bool
     */
    public function getIsPartiallyRefundedAttribute(): bool
    {
        return $this->amount_refunded > 0 && $this->amount_refunded < $this->amount;
    }

    /* ==========================================
       Helper Methods
       ========================================== */

    /**
     * Mark payment as completed.
     * Updates status, sets paid_at timestamp, and merges gateway response.
     *
     * @param array $gatewayResponse Additional gateway response data
     * @return bool
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
     * Mark payment as failed.
     * Updates status, sets failed_at timestamp, and records error details.
     *
     * @param string|null $errorCode Error code from gateway
     * @param string|null $errorMessage Error message from gateway
     * @return bool
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
     * Record a refund for this payment.
     * Updates refunded amount, status, and gateway response.
     *
     * @param float $amount Amount to refund
     * @param array $gatewayResponse Gateway refund response data
     * @return bool
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
     * Get payment status badge color for UI.
     * Returns Bootstrap color class based on status.
     *
     * @return string Color class (success, warning, danger, info, secondary)
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
     * Get human-readable payment status label.
     * Returns translated status text.
     *
     * @return string
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
