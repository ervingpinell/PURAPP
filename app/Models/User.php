<?php

namespace App\Models;

use Illuminate\Contracts\Translation\HasLocalePreference;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Notificaciones nativas (sin colas)
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\EmailChangeVerificationNotification;

// Sanctum (/api)
use Laravel\Sanctum\HasApiTokens;
// 2FA (Fortify)
use Laravel\Fortify\TwoFactorAuthenticatable;
// Spatie Permission
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 *
 * Represents a registered user in the system.
 * Handles authentication, authorization, 2FA, and user preferences.
 *
 * @property int $user_id Primary key
 * @property string $email Email address (unique)
 * @property string|null $pending_email New email pending verification
 * @property string|null $email_change_token Token for email change verification
 * @property \Carbon\Carbon|null $email_change_token_expires_at Token expiration
 * @property \Carbon\Carbon|null $email_verified_at Email verification timestamp
 * @property string $password Hashed password
 * @property string $first_name User's first name
 * @property string $last_name User's last name
 * @property-read string $full_name Full name (first + last) - MUTATOR/ACCESSOR
 * @property string|null $country_code Phone country code
 * @property string|null $phone Phone number
 * @property string|null $locale Preferred locale (en, es, fr, pt, de)
 * @property bool $is_guest Whether user is a guest account
 * @property bool $is_active Account active status
 * @property string|null $two_factor_secret 2FA secret key
 * @property string|null $two_factor_recovery_codes 2FA recovery codes (encrypted)
 * @property \Carbon\Carbon|null $two_factor_confirmed_at When 2FA was confirmed
 * @property string|null $remember_token Remember me token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at Soft delete timestamp
 *
 * @property-read string $full_phone Phone with country code
 * @property-read string $e164_phone Phone in E.164 format
 * @property-read bool $two_factor_enabled Whether 2FA is enabled
 * @property-read bool $two_factor_confirmed Whether 2FA is confirmed
 * @property-read \Illuminate\Database\Eloquent\Collection|Cart[] $cart
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 */
class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;
    use HasRoles;
    use Prunable;
    use SoftDeletes;

    /**
     * Relación con Carts
     */
    public function cart()
    {
        return $this->hasMany(Cart::class, 'user_id', 'user_id');
    }

    /**
     * Relación con Password Setup Tokens
     */
    public function passwordSetupTokens()
    {
        return $this->hasMany(PasswordSetupToken::class, 'user_id', 'user_id');
    }

    /**
     * Tabla y clave primaria personalizadas
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * Asignación masiva
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
        'phone',
        'country_code',
        'is_locked',
        'is_super_admin',
        'locale', // New field
        'address',
        'city',
        'state',
        'zip',
        'country',
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
        'deleted_at'                => 'datetime',
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

    // ...

    /**
     * Get the user's preferred locale.
     */
    public function preferredLocale(): string
    {
        return $this->locale ?? app()->getLocale();
    }

    // ...

    // Accessor for full_name (lectura)
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // Mutator for full_name (escritura - safety net)
    public function setFullNameAttribute($value)
    {
        $parts = explode(' ', $value, 2);
        $this->first_name = $parts[0];
        $this->last_name  = $parts[1] ?? '';
        // No setear attributes['full_name'] para evitar error de columna inexistente
    }

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
            $this->notify(new \App\Notifications\ResetPasswordNotification($token));
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
            $notification = new VerifyEmail;

            // Strict Locale Logic: Only ES gets Spanish, everyone else gets English
            $locale = $this->preferredLocale();
            $notification->locale(($locale === 'es') ? 'es' : 'en');

            // VerifyEmail no implementa ShouldQueue -> se envía en sync
            $this->notify($notification);
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
