<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'direccion',
    ];

    public function reservas()
    {
        // Relación uno a muchos con la tabla reservas
        // Un cliente puede tener muchas reservas
        // y una reserva pertenece a un cliente
        // La clave foránea en la tabla reservas es 'id_cliente'
        // y la clave primaria en la tabla clientes es 'id_cliente'
        return $this->hasMany(Reserva::class, 'id_cliente');
    }
}
