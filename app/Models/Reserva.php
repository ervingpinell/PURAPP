<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_cliente',
        'id_tour',
        'precio_adulto',
        'precio_nino',
        'fecha_reserva',
        'fecha_inicio',
        'fecha_fin',
        'estado_reserva',
        'idioma_tour',
        'notas',
        'codigo_reserva',
        'cantidad_adultos',
        'cantidad_ninos',
        'total_pago',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'id_tour');
    }
}
