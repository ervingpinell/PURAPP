<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'user_id',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Expiration helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Returns true if the cart is expired (now >= expires_at) or expires_at is missing.
     */
    public function isExpired(): bool
    {
        return !$this->expires_at || now()->greaterThanOrEqualTo($this->expires_at);
    }

    /**
     * Returns remaining seconds before expiration (0 if expired or no expires_at).
     */
    public function remainingSeconds(): int
    {
        if (!$this->expires_at) {
            return 0;
        }
        return max(0, now()->diffInSeconds($this->expires_at, false));
    }

    /**
     * Refreshes the expiration timestamp by the given number of minutes.
     * Defaults to config('cart.expiry_minutes', 15).
     */
    public function refreshExpiry(?int $minutes = null): self
    {
        $minutes = $minutes ?? (int) config('cart.expiry_minutes', 15);
        $this->expires_at = now()->addMinutes($minutes);
        $this->save();

        return $this;
    }

    /**
     * Scope: active and not expired (strictly future expires_at).
     */
    public function scopeActiveNotExpired($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now());
    }
}
