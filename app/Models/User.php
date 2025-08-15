<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'full_name','email','password','status','role_id','phone','country_code',
    ];

    protected $hidden = ['password','remember_token'];

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id')->where('is_active', true);
    }

    // AdminLTE helpers
    public function adminlte_desc()
    {
        return $this->role ? $this->role->role_name : 'Sin rol';
    }

    public function adminlte_profile_url()
    {
        return route('profile.edit');
    }

    public function adminlte_image()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name);
    }

    // Alias name
    public function name(): Attribute
    {
        return Attribute::get(fn () => $this->full_name);
    }

    // ── Mutators de normalización ───────────────────────────────────────────────
    protected function countryCode(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if ($value === null || $value === '') return null;
                $digits = preg_replace('/\D+/', '', (string)$value);
                if ($digits === '') return null;
                // máx 4 dígitos para el código de país
                $digits = substr($digits, 0, 4);
                return '+' . $digits;
            }
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if ($value === null || $value === '') return null;
                // Solo dígitos para el número nacional
                $digits = preg_replace('/\D+/', '', (string)$value);
                return $digits !== '' ? $digits : null;
            }
        );
    }

    // ── Accesores prácticos ────────────────────────────────────────────────────
public function getFullPhoneAttribute(): ?string
{
    if ($this->country_code && $this->phone) {
        return "{$this->country_code} {$this->phone}";
    }
    return $this->phone ?? $this->country_code ?? null;
}

    public function getE164PhoneAttribute(): ?string
    {
        if (!$this->phone && !$this->country_code) return null;
        return trim(($this->country_code ?? '').($this->phone ?? ''));
    }
}
