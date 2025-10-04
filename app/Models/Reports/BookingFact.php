<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class BookingFact extends Model
{
    protected $table = 'v_booking_facts';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}
