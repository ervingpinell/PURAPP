<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_role';
    public $timestamps = false;

    protected $fillable = [
        'role_name',
        'description',
    ];

    // Relación con usuarios (1:N)
    public function users()
    {
        return $this->hasMany(User::class, 'id_role', 'id_role');
    }
}

