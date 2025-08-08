<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourType extends Model
{
 protected $table = 'tour_types';
    protected $primaryKey = 'tour_type_id';
    public $incrementing = true;
    protected $keyType = 'int';

protected $fillable = [
    'name',
    'description',
    'duration',
    'is_active'
];

    public $timestamps = true;

    public function tours()
    {
        return $this->hasMany(Tour::class, 'tour_type_id');
    }

    public function getRouteKeyName()
{
    return 'tour_type_id';
}
}
