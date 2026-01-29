<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ReviewRequest Model
 *
 * Tracks review requests sent to customers.
 */
class ReviewRequest extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'booking_id','user_id','product_id',
        'email','token','status',
        'sent_at','reminded_at','expires_at',
    ];

    protected $casts = [
        'sent_at'     => 'datetime',
        'reminded_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

public function user() { return $this->belongsTo(\App\Models\User::class, 'user_id','user_id'); }
public function product() { return $this->belongsTo(\App\Models\Product::class, 'product_id','product_id'); }
public function booking() { return $this->belongsTo(\App\Models\Booking::class, 'booking_id','booking_id'); }

}
