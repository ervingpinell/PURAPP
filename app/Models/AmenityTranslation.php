<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmenityTranslation extends Model
{
    protected $table = 'amenity_translations';
    public $timestamps = true;

    protected $fillable = [
        'amenity_id',
        'locale',
        'name',
    ];

    public function amenity()
    {

        return $this->belongsTo(Amenity::class, 'amenity_id', 'amenity_id');
    }
}
