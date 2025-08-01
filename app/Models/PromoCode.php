<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'discount_amount',
        'discount_percent',
        'is_used',
        'used_at',
        'used_by_booking_id',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Aplica el descuento y devuelve el nuevo total.
     */
    public function applyDiscount(float $total): float
    {
        if ($this->discount_amount) {
            return max($total - $this->discount_amount, 0);
        }

        if ($this->discount_percent) {
            return max($total - ($total * ($this->discount_percent / 100)), 0);
        }

        return $total;
    }

    /**
     * Marca este código como usado.
     */
    public function markAsUsed(int $bookingId): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
            'used_by_booking_id' => $bookingId,
        ]);
    }
}
