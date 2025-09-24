<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
    ];

    // =========================
    // Relaciones
    // =========================
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // =========================
    // Helpers de expiración
    // =========================
    /**
     * Verifica si el carrito ya expiró.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Devuelve segundos restantes antes de expirar.
     */
    public function remainingSeconds(): int
    {
        if (!$this->expires_at) {
            return 0;
        }
        return max(0, now()->diffInSeconds($this->expires_at, false));
    }

    /**
     * Refresca la expiración (ej. +15 minutos).
     */
    public function refreshExpiry(?int $minutes = null): void
    {
        $minutes = $minutes ?? config('cart.expiry_minutes', 15);
        $this->expires_at = now()->addMinutes($minutes);
        $this->save();
    }

}
