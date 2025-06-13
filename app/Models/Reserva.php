<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'user_id',
        'id_tour',
        'fecha_reserva',
        'fecha_inicio',
        'fecha_fin',
        'estado_reserva',
        'idioma_tour',
        'precio_adulto',
        'precio_nino',
        'cantidad_adultos',
        'cantidad_ninos',
        'total_pago',
        'codigo_reserva',
        'notas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'id_tour');
    }
}
