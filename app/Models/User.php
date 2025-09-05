<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\AsEncryptedCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

// Notificaciones nativas (NO usan colas)
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;

// Sanctum (/api)
use Laravel\Sanctum\HasApiTokens;
// 2FA (Fortify)
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use Prunable;

    /**
     * Tabla y clave primaria personalizadas
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * Asignación masiva
     */
    protected $fillable = [
        'full_name', 'email', 'password', 'status', 'role_id',
        'phone', 'country_code', 'is_locked',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'status'            => 'boolean',
        'is_locked'         => 'boolean',
        'email_verified_at' => 'datetime',

        // 2FA (Fortify)
        'two_factor_secret'         => 'encrypted',
        'two_factor_recovery_codes' => AsEncryptedCollection::class,
        'two_factor_confirmed_at'   => 'datetime',
    ];

    /**
     * Ocultos
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /* ============================================================
     | Relaciones
     * ============================================================*/
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id')->where('is_active', true);
    }

    /* ============================================================
     | Flags / Helpers
     * ============================================================*/
    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
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

    /* ============================================================
     | Accessors / Mutators
     * ============================================================*/

    // Fortify/Notifications suelen leer ->name; mapeamos a full_name
    public function name(): Attribute
    {
        return Attribute::get(fn () => $this->full_name);
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? mb_strtolower(trim($value)) : $value
        );
    }

    protected function countryCode(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if ($value === null || $value === '') return null;
                $digits = preg_replace('/\D+/', '', (string) $value);
                if ($digits === '') return null;
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
                $digits = preg_replace('/\D+/', '', (string) $value);
                return $digits !== '' ? $digits : null;
            }
        );
    }

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
        return trim(($this->country_code ?? '') . ($this->phone ?? ''));
    }

    /* ============================================================
     | Notificaciones (100% SIN COLAS)
     * ============================================================*/

    public function sendPasswordResetNotification($token): void
    {
        try {
            // Enviar inmediatamente (ignora cualquier config de colas)
            $this->notifyNow(new ResetPassword($token));
        } catch (\Throwable $e) {
            Log::warning('Error al enviar reset password (sync)', [
                'user_id' => $this->getKey(),
                'email'   => $this->email,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function sendEmailVerificationNotification(): void
    {
        try {
            // Enviar inmediatamente (ignora cualquier config de colas)
            $this->notifyNow(new VerifyEmail);
        } catch (\Throwable $e) {
            Log::warning('Error al enviar verificación de email (sync)', [
                'user_id' => $this->getKey(),
                'email'   => $this->email,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /* ============================================================
     | Prunable: elimina usuarios no verificados antiguos
     * ============================================================*/

    /**
     * Selecciona los registros a podar:
     * - Sin verificar
     * - Antigüedad > N días (configurable en config/auth.php -> 'unverified_prune_days')
     */
    public function prunable()
    {
        $days = (int) config('auth.unverified_prune_days', 7);

        return static::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Hook previo a borrar cada registro (logging opcional).
     */
    protected function pruning()
    {
        Log::info('Pruning unverified user', [
            'user_id' => $this->getKey(),
            'email'   => $this->email,
        ]);
    }
}
