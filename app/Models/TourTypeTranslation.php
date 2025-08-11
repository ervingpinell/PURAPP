<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourTypeTranslation extends Model
{
    protected $table = 'tour_type_translations';

    protected $primaryKey = 'id'; // O ajusta si tu PK es diferente
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'tour_type_id',
        'locale',
        'name',
        'description',
        'duration', // ✅ Incluido porque ahora traduces este campo
    ];

    public function tourType()
    {
        return $this->belongsTo(TourType::class, 'tour_type_id', 'tour_type_id');
    }
}
