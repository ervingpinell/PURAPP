<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = 'payment_methods';
    protected $primaryKey = 'payment_method_id';

    protected $fillable = [
        'user_id',
        'gateway',
        'gateway_customer_id',
        'gateway_payment_method_id',
        'type',
        'card_brand',
        'card_last4',
        'card_exp_month',
        'card_exp_year',
        'card_fingerprint',
        'bank_name',
        'account_last4',
        'is_default',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /* ==========================================
       Relationships
       ========================================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /* ==========================================
       Scopes
       ========================================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeCards($query)
    {
        return $query->where('type', 'card');
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /* ==========================================
       Accessors & Mutators
       ========================================== */

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'card') {
            $brand = ucfirst($this->card_brand ?? 'Card');
            return "{$brand} •••• {$this->card_last4}";
        }

        if ($this->type === 'bank_account') {
            return "{$this->bank_name} •••• {$this->account_last4}";
        }

        return ucfirst($this->type);
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->type !== 'card' || !$this->card_exp_month || !$this->card_exp_year) {
            return false;
        }

        $expiry = \Carbon\Carbon::createFromDate($this->card_exp_year, $this->card_exp_month, 1)->endOfMonth();
        return $expiry->isPast();
    }

    public function getExpiryDateAttribute(): ?string
    {
        if ($this->type !== 'card' || !$this->card_exp_month || !$this->card_exp_year) {
            return null;
        }

        return sprintf('%02d/%s', $this->card_exp_month, substr($this->card_exp_year, -2));
    }

    /* ==========================================
       Helper Methods
       ========================================== */

    /**
     * Set this payment method as default for the user
     */
    public function setAsDefault(): bool
    {
        // Remove default flag from other payment methods
        static::where('user_id', $this->user_id)
            ->where('payment_method_id', '!=', $this->payment_method_id)
            ->update(['is_default' => false]);

        return $this->update(['is_default' => true]);
    }

    /**
     * Get card brand icon class
     */
    public function getCardBrandIconAttribute(): string
    {
        return match (strtolower($this->card_brand ?? '')) {
            'visa' => 'fab fa-cc-visa',
            'mastercard' => 'fab fa-cc-mastercard',
            'amex', 'american express' => 'fab fa-cc-amex',
            'discover' => 'fab fa-cc-discover',
            'diners' => 'fab fa-cc-diners-club',
            'jcb' => 'fab fa-cc-jcb',
            default => 'fas fa-credit-card',
        };
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a new payment method, if it's the first one, make it default
        static::creating(function ($paymentMethod) {
            if ($paymentMethod->is_default === null) {
                $hasDefault = static::where('user_id', $paymentMethod->user_id)
                    ->where('is_default', true)
                    ->exists();

                $paymentMethod->is_default = !$hasDefault;
            }
        });
    }
}
