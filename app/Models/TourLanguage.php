<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourLanguage extends Model
{
    protected $primaryKey = 'tour_language_id'; 

    protected $fillable = [
        'name', 'is_active'
    ];

    public $timestamps = true; 
}
