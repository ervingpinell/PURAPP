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
        'extended_count',
        'last_extended_at',
    ];

    protected $casts = [
        'expires_at'       => 'datetime',
        'last_extended_at' => 'datetime',
        'is_active'        => 'boolean',
        'extended_count'   => 'integer',
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
        return (int) config('cart.expiry_minutes', 15);
    }

    public function extendMinutes(): int
    {
        // Unificado con frontend/controlador (15 por defecto)
        return (int) config('cart.extend_minutes', 15);
    }

    public function maxExtensions(): int
    {
        return (int) config('cart.max_extensions', 1);
    }

    /* ---------------- Helper de límite ---------------- */
    public function isExtensionLimitReached(): bool
    {
        return (int) $this->extended_count >= $this->maxExtensions();
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

    /** ¿Aún puede extenderse (botón “Extender” del timer)? */
    public function canExtend(): bool
    {
        if ($this->isExpired()) return false;
        return !$this->isExtensionLimitReached();
    }

    /**
     * Extiende el hold UNA vez respetando límites (cuenta en extended_count).
     * Versión segura frente a clics concurrentes (UPDATE condicional).
     *
     * @return bool true si extendió, false si no pudo
     */
    public function extendOnce(?int $minutes = null): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        $minutes   = $minutes ?? $this->extendMinutes();
        $now       = now();
        $base      = $this->expires_at && $this->expires_at->gt($now) ? $this->expires_at->copy() : $now;
        $newExpiry = $base->addMinutes($minutes);

        // Intento atómico: solo si no rebasamos el límite
        $affected = DB::table($this->getTable())
            ->where($this->getKeyName(), $this->getKey())
            ->where('is_active', true)
            ->where('extended_count', '<', $this->maxExtensions())
            ->update([
                'expires_at'       => $newExpiry,
                'extended_count'   => DB::raw('extended_count + 1'),
                'last_extended_at' => $now,
                'updated_at'       => $now,
            ]);

        if ($affected === 1) {
            // refrescar el modelo en memoria
            $this->expires_at       = $newExpiry;
            $this->extended_count   = ((int) $this->extended_count) + 1;
            $this->last_extended_at = $now;
            return true;
        }

        return false;
    }

    /** Marca el carrito como expirado y lo limpia. */
    public function forceExpire(): void
    {
        $this->items()->delete();
        $this->is_active  = false;
        $this->expires_at = now();
        $this->save();
    }

    /* ---------------- Helper de estado (para Blade o API) ---------------- */
    public function extensionState(): array
    {
        return [
            'used'       => (int) $this->extended_count,
            'max'        => $this->maxExtensions(),
            'remaining'  => max(0, $this->maxExtensions() - (int) $this->extended_count),
            'can_extend' => $this->canExtend(),
        ];
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
