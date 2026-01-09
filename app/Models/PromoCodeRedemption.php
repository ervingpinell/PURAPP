<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * PromoCodeRedemption Model
 *
 * Tracks promo code usage per booking.
 */
class PromoCodeRedemption extends Model
{
    protected $table = 'promo_code_redemptions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'promo_code_id','booking_id','user_id','used_at',
        'applied_amount','operation_snapshot','percent_snapshot','amount_snapshot',
    ];

    protected $casts = [
        'promo_code_id' => 'int',
        'booking_id'    => 'int',
        'user_id'       => 'int',
        'used_at'       => 'datetime',
        'applied_amount'     => 'float',
        'percent_snapshot'   => 'float',
        'amount_snapshot'    => 'float',
    ];

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
