<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    protected $fillable = ['name', 'guard_name', 'description', 'is_active'];
}
