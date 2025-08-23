<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourLanguage extends Model
{
    use HasFactory;

    protected $primaryKey = 'tour_language_id';


    protected $table = 'tour_languages';

    protected $fillable = [
        'name',
        'is_active'
    ];

    public $timestamps = true;
    public function getRouteKeyName()
    {
        return 'tour_language_id';
    }
}
