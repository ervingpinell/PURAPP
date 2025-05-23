<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $primaryKey = 'id_tour';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_adulto',
        'precio_nino',
        'duracion_horas',
        'ubicacion',
        'tipo_tour',
        'idioma_disponible',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_tour');
    }
}
