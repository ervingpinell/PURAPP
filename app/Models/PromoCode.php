<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PromoCode extends Model
{
    protected $table = 'promo_codes';
    protected $primaryKey = 'id'; // PK real según tus migraciones
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'code',
        'discount_percent',
        'discount_amount',
        'is_used',
        'used_at',
        'used_by_booking_id',
        'valid_from',
        'valid_until',
        'usage_limit',
        'usage_count',
        'operation',
    ];

    protected $casts = [
        'discount_percent' => 'float',
        'discount_amount'  => 'float',
        'is_used'          => 'bool',
        'used_at'          => 'datetime',
        'valid_from'       => 'date',
        'valid_until'      => 'date',
        'usage_limit'      => 'integer',
        'usage_count'      => 'integer',
        'operation'        => 'string',
    ];

    protected $appends = ['remaining_uses'];

    // Compatibilidad: permite $promo->promo_code_id
    public function getPromoCodeIdAttribute(): int
    {
        return (int) $this->attributes['id'];
    }

    // Relaciones
    public function redemptions()
    {
        return $this->hasMany(PromoCodeRedemption::class, 'promo_code_id', 'id');
    }

    // Utils
    public static function normalize(string $code): string
    {
        return trim(preg_replace('/\s+/', '', $code));
    }

    public function isValidToday(?string $tz = null): bool
    {
        $tz    = $tz ?: config('app.timezone', 'America/Costa_Rica');
        $today = Carbon::today($tz);

        $startsOk = !$this->valid_from  || $today->greaterThanOrEqualTo(Carbon::parse($this->valid_from,  $tz));
        $endsOk   = !$this->valid_until || $today->lessThanOrEqualTo(Carbon::parse($this->valid_until, $tz));

        return $startsOk && $endsOk;
    }

    public function hasRemainingUses(): bool
    {
        return is_null($this->usage_limit) || $this->usage_count < $this->usage_limit;
    }

    public function getRemainingUsesAttribute(): ?int
    {
        return is_null($this->usage_limit)
            ? null
            : max(0, (int)$this->usage_limit - (int)$this->usage_count);
    }

    /** Registrar un uso (idempotente por booking) */
    public function redeemForBooking(int $bookingId, ?int $userId = null): void
    {
        DB::transaction(function () use ($bookingId, $userId) {
            /** @var self $promo */
            $promo = self::lockForUpdate()->findOrFail($this->getKey());

            if (! $promo->hasRemainingUses()) {
                throw ValidationException::withMessages([
                    'promo_code' => 'Este código alcanzó su límite de usos.',
                ]);
            }

            $exists = $promo->redemptions()->where('booking_id', $bookingId)->exists();
            if (! $exists) {
                $promo->redemptions()->create([
                    'booking_id' => $bookingId,
                    'user_id'    => $userId,
                    'used_at'    => now(),
                ]);

                $promo->usage_count += 1;

                // Si hay límite y se agotó, marca flags
                if (!is_null($promo->usage_limit) && $promo->usage_count >= $promo->usage_limit) {
                    $promo->is_used = true;
                    $promo->used_at = now();
                }

                // (opcional) legacy
                $promo->used_by_booking_id = $bookingId;

                $promo->save();
            }
        });
    }

    /** Revertir uso al quitar el cupón de una reserva */
    public function revokeRedemptionForBooking(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId) {
            /** @var self $promo */
            $promo = self::lockForUpdate()->findOrFail($this->getKey());

            $deleted = $promo->redemptions()->where('booking_id', $bookingId)->delete();
            if ($deleted) {
                $promo->usage_count = max(0, (int)$promo->usage_count - 1);

                if (is_null($promo->usage_limit) || $promo->usage_count < $promo->usage_limit) {
                    $promo->is_used = false;
                    $promo->used_by_booking_id = null;
                }

                $promo->save();
            }
        });
    }
}
