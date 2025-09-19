<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewRequest extends Model
{
    protected $fillable = [
        'booking_id','user_id','tour_id',
        'email','token','status',
        'sent_at','reminded_at','expires_at',
    ];

    protected $casts = [
        'sent_at'     => 'datetime',
        'reminded_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function booking() { return $this->belongsTo(\App\Models\Booking::class); }
    public function user()    { return $this->belongsTo(\App\Models\User::class); }
    public function tour()    { return $this->belongsTo(\App\Models\Tour::class); }
}
