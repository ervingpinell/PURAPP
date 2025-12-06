<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Notificaciones nativas (sin colas)
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\EmailChangeVerificationNotification;

// Sanctum (/api)
use Laravel\Sanctum\HasApiTokens;
// 2FA (Fortify)
use Laravel\Fortify\TwoFactorAuthenticatable;
// Spatie Permission
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int         $user_id
 * @property string      $full_name
 * @property string      $email
 * @property string|null $password
 * @property bool        $status
 * @property int|null    $role_id
 * @property string|null $phone
 * @property string|null $country_code
 * @property bool        $is_locked
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 *
 * Métodos del trait de Fortify (disponibles en runtime):
 * @method array  recoveryCodes()
 * @method string twoFactorQrCodeSvg()
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;
    use HasRoles;
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
        'full_name',
        'email',
        'password',
        'status',
        'phone',
        'country_code',
        'is_locked',
        'is_super_admin',
    ];

    /**
     * Casts
     *
     * Importante: Fortify guarda estos campos como JSON encriptado.
     */
    protected $casts = [
        'status'                    => 'boolean',
        'is_locked'                 => 'boolean',
        'is_super_admin'            => 'boolean',
        'email_verified_at'         => 'datetime',
        'two_factor_secret'         => 'encrypted',
        'two_factor_recovery_codes' => 'encrypted',
        'two_factor_confirmed_at'   => 'datetime',
        'pending_email_created_at'  => 'datetime',
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

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    // AdminLTE helpers
    public function adminlte_desc()
    {
        if ($this->isSuperAdmin()) {
            return 'Super Admin';
        }
        return $this->getRoleNames()->first() ?? 'Sin rol';
    }

    public function adminlte_profile_url()
    {
        return $this->canDo('access-admin')
            ? route('admin.profile.edit')
            : route('profile.edit');
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
        return Attribute::get(fn() => $this->full_name);
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value ? mb_strtolower(trim($value)) : $value
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
        return Attribute::Make(
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
     | 2FA Helpers (seguros)
     * ============================================================*/

    public function twoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret);
    }

    public function twoFactorConfirmed(): bool
    {
        return !empty($this->two_factor_confirmed_at);
    }

    public function safeTwoFactorQrCodeSvg(): ?string
    {
        try {
            return method_exists($this, 'twoFactorQrCodeSvg') ? $this->twoFactorQrCodeSvg() : null;
        } catch (\Throwable $e) {
            Log::warning('2FA QR SVG fail', ['uid' => $this->getKey(), 'err' => $e->getMessage()]);
            return null;
        }
    }

    public function safeRecoveryCodes(): array
    {
        try {
            $codes = method_exists($this, 'recoveryCodes') ? $this->recoveryCodes() : [];

            return collect($codes ?: [])
                ->flatten()
                ->map(function ($c) {
                    if (is_string($c)) return $c;
                    if (is_array($c))  return implode('', array_map('strval', $c));
                    return (string) $c;
                })
                ->filter()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('2FA recovery codes decrypt fail', ['uid' => $this->getKey(), 'err' => $e->getMessage()]);
            return [];
        }
    }

    /* ============================================================
     | Notificaciones (SYNC, sin colas)
     * ============================================================*/

    public function sendPasswordResetNotification($token): void
    {
        try {
            // ResetPassword no implementa ShouldQueue -> se envía en sync
            $this->notify(new ResetPassword($token));
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
            // VerifyEmail no implementa ShouldQueue -> se envía en sync
            $this->notify(new VerifyEmail);
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

    public function prunable()
    {
        $days = (int) config('auth.unverified_prune_days', 7);

        return static::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays($days));
    }

    protected function pruning()
    {
        Log::info('Pruning unverified user', [
            'user_id' => $this->getKey(),
            'email'   => $this->email,
        ]);
    }

    /* ============================================================
     | Backwards-compat (si aún se usa en vistas/old code)
     * ============================================================*/
    public function isAdmin(): bool
    {
        // Super admins siempre tienen acceso de admin
        if ($this->isSuperAdmin()) {
            return true;
        }
        // Usar Spatie para verificar rol de admin
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->hasAnyRole(['admin', 'supervisor']);
    }

    /* ============================================================
     | RBAC helpers - Ahora usando Spatie
     * ============================================================*/

    /**
     * Slug de rol estable - Ahora usa Spatie como fuente principal
     */
    public function roleSlug(): string
    {
        if ($this->isSuperAdmin()) {
            return 'super-admin';
        }

        // Usar Spatie como fuente principal
        $spatieRole = $this->roles()->first();
        if ($spatieRole) {
            return $spatieRole->name;
        }

        return 'guest';
    }

    /**
     * ¿Puede ejercer una "ability" (permiso)?
     * Ahora usa Spatie permissions
     */
    public function canDo(string $ability): bool
    {
        // Super admin puede hacer todo
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Usar Spatie para verificar permisos
        return $this->can($ability);
    }

    public function sendEmailChangeVerificationNotification(string $token, ?string $locale = null): void
    {
        $this->notify(new EmailChangeVerificationNotification(
            $token,
            $locale ?? app()->getLocale()
        ));
    }


    /**
     * Si existe pending_email, las notificaciones de cambio de correo
     * se envían a ese correo en lugar del actual.
     */
    public function routeNotificationForMail($notification): ?string
    {
        if ($notification instanceof EmailChangeVerificationNotification && $this->pending_email) {
            return $this->pending_email;
        }

        return $this->email;
    }
}
