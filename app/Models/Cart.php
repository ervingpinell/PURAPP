<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /* ---------------- Relationships ---------------- */
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /* ---------------- Config helpers ---------------- */
    public function expiryMinutes(): int
    {
        // Try to get from database settings first, then fall back to config
        $setting = \App\Models\Setting::where('key', 'cart.expiration_minutes')->value('value');
        return (int) ($setting ?? config('cart.expiration_minutes', 30));
    }

    /* ---------------- Expiration helpers ---------------- */
    /** ¿Está expirado? (ahora >= expires_at) o no tiene expires_at */
    public function isExpired(): bool
    {
        return !$this->expires_at || now()->greaterThanOrEqualTo($this->expires_at);
    }

    /** Segundos restantes (0 si expirado o sin expires_at) */
    public function remainingSeconds(): int
    {
        if (!$this->expires_at) return 0;
        return max(0, now()->diffInSeconds($this->expires_at, false));
    }

    /**
     * Asegura que el carrito tenga una expiración hacia el futuro.
     * - Si no hay `expires_at` o ya venció: fija ahora + $minutes
     * - Si existe y es menor que ahora + $minutes: empuja hasta ese valor (no acorta)
     * Devuelve $this para encadenar.
     */
    public function ensureExpiry(?int $minutes = null): self
    {
        $minutes = $minutes ?? $this->expiryMinutes();
        $target  = now()->addMinutes($minutes);

        if (!$this->expires_at || $this->expires_at->lte(now()) || $this->expires_at->lt($target)) {
            $this->expires_at = $target;
            $this->save();
        }
        return $this;
    }

    /**
     * Refresca/“empuja” la expiración si está por debajo de (now + minutes).
     * No cuenta como “extensión” del límite — úsalo al agregar/borrar items.
     */
    public function refreshExpiry(?int $minutes = null): self
    {
        return $this->ensureExpiry($minutes ?? $this->expiryMinutes());
    }

    /** Marca el carrito como expirado y lo limpia. */
    public function forceExpire(): void
    {
        $this->items()->delete();
        $this->is_active  = false;
        $this->expires_at = now();
        $this->save();
    }

    /* ---------------- Pricing helpers ---------------- */
    /**
     * Calculate the total price from the cart's stored snapshot.
     * This uses the prices that were saved when items were added to the cart,
     * which were calculated with the correct tour_date.
     * 
     * @return float Total price including taxes
     */
    public function calculateTotal(): float
    {
        $total = 0.0;

        foreach ($this->items as $item) {
            $cats = collect($item->categories ?? []);

            if ($cats->isNotEmpty()) {
                foreach ($cats as $cat) {
                    // Use the price already stored in the cart snapshot
                    $catQty = (int)($cat['quantity'] ?? 0);
                    $catPrice = (float)($cat['price'] ?? 0);

                    // If tax_breakdown exists in snapshot, use it
                    if (isset($cat['tax_breakdown']) && is_array($cat['tax_breakdown'])) {
                        $total += (float)($cat['tax_breakdown']['total'] ?? 0);
                    } else {
                        // Fallback: calculate from stored price
                        $total += $catPrice * $catQty;
                    }
                }
            } else {
                // Legacy: adult/kid prices (should not happen with new system)
                $adultQty = (int)($item->adults_quantity ?? 0);
                $kidQty = (int)($item->kids_quantity ?? 0);

                if ($adultQty > 0) {
                    $adultPrice = $item->tour->prices->where('category.slug', 'adult')->first();
                    if ($adultPrice) {
                        $breakdown = $adultPrice->calculateTaxBreakdown($adultQty);
                        $total += $breakdown['total'];
                    }
                }

                if ($kidQty > 0) {
                    $kidPrice = $item->tour->prices->whereIn('category.slug', ['kid', 'child'])->first();
                    if ($kidPrice) {
                        $breakdown = $kidPrice->calculateTaxBreakdown($kidQty);
                        $total += $breakdown['total'];
                    }
                }
            }
        }

        return $total;
    }

    /* ---------------- Scopes útiles ---------------- */
    public function scopeActiveNotExpired($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now());
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeLatestActive($query)
    {
        return $query->where('is_active', true)->latest('cart_id');
    }
}
