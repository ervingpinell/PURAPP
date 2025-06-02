<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;


class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'status',
        'id_role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // A user belongs to a role.
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function adminlte_desc()
    {
        return $this->role ? $this->role->role_name : 'Sin rol';
    }

    public function adminlte_profile_url()
    {
        return route('profile.edit'); 
    }
    //adminlte_image can be used only by enabling the function in config/adminlte.php → set the option 'usermenu_image' => true instead of false.
    public function adminlte_image()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name);
    }
    public function name(): Attribute
    {
        return Attribute::get(fn () => $this->full_name);
    }

    
    

}
