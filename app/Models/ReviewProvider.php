<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

class ReviewProvider extends Model
{
    protected $table = 'review_providers';

    protected $fillable = [
        'name', 'slug', 'driver', 'indexable', 'settings', 'cache_ttl_sec', 'is_active',
    ];

    protected $casts = [
        'indexable'     => 'boolean',
        'is_system'     => 'boolean',
        'is_active'     => 'boolean',
        'cache_ttl_sec' => 'integer',
        'settings'      => 'array',
    ];

    /**
     * Claves que se cifran automáticamente si llegan en claro dentro de settings.
     */
    public const SECRET_KEYS = [
        'api_key', 'api_secret', 'client_secret', 'access_token', 'refresh_token',
    ];

public const LOCAL_SLUG = 'local';
       private const LOCAL_ALLOWED_SETTINGS = ['min_stars', 'auto_publish'];

        protected static function booted(): void
    {
        // Saneo / invariantes ANTES de guardar
        static::saving(function (ReviewProvider $m) {
            // ¿Es el local o es de sistema?
            if (($m->slug === self::LOCAL_SLUG) || ($m->is_system ?? false)) {
                // Fuerza invariantes
                $m->slug      = self::LOCAL_SLUG;
                $m->driver    = 'local';
                $m->is_system = true;
                $m->is_active = true;

                // Sanea settings: solo min_stars (+ auto_publish si lo usas)
                $s = (array) ($m->settings ?? []);

                // Normaliza min_stars 0..5
                $min = (int) data_get($s, 'min_stars', 0);
                $min = max(0, min(5, $min));

                // (Opcional) conservar auto_publish si lo usas en tu flujo
                $auto = (bool) data_get($s, 'auto_publish', data_get($m->attributes, 'auto_publish', true));

                $sanitized = ['min_stars' => $min];
                if (array_key_exists('auto_publish', $s)) {
                    $sanitized['auto_publish'] = $auto;
                }

                $m->settings = $sanitized; // re-dispara mutator y cifra secretos si los hubiera
            }
        });

        // Bloquea eliminar el local
        static::deleting(function (ReviewProvider $m) {
            if ($m->slug === self::LOCAL_SLUG || ($m->is_system ?? false)) {
                throw new \RuntimeException("El proveedor 'local' es de sistema y no puede eliminarse.");
            }
        });
    }

    /**
     * Mutator: cifra llaves sensibles que lleguen en claro en settings.* y
     * las mueve a settings.secrets.{key}. También acepta *_encrypted.
     */
    public function setSettingsAttribute($value): void
    {
        $arr = (array) $value;
        $secrets = (array) Arr::get($arr, 'secrets', []);

        // Cifrar claves en claro
        foreach (self::SECRET_KEYS as $key) {
            if (array_key_exists($key, $arr) && filled($arr[$key])) {
                $secrets[$key] = Crypt::encryptString((string) $arr[$key]);
                unset($arr[$key]);
            }
        }

        // Normalizar *_encrypted hacia secrets[key] (si ya vienen cifradas)
        foreach ($arr as $k => $v) {
            if (str_ends_with($k, '_encrypted') && filled($v)) {
                $plainKey = substr($k, 0, -10);
                $secrets[$plainKey] = (string) $v; // asumimos ya cifrado
                unset($arr[$k]);
            }
        }

        if (!empty($secrets)) {
            $arr['secrets'] = $secrets;
        }

        // Guardamos como JSON; el cast 'array' lo devolverá como array al leer.
        $this->attributes['settings'] = json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Leer un secreto descifrado (o default si no existe / no descifra).
     */
    public function secret(string $key, $default = null): ?string
    {
        $val = Arr::get($this->settings ?? [], "secrets.$key");
        if (!$val) return $default;

        try {
            return Crypt::decryptString((string) $val);
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Escribir/actualizar un secreto (si $value es null/empty, lo elimina).
     */
    public function putSecret(string $key, ?string $value): void
    {
        $settings = (array) ($this->settings ?? []);
        $secrets  = (array) Arr::get($settings, 'secrets', []);

        if ($value === null || $value === '') {
            unset($secrets[$key]);
        } else {
            $secrets[$key] = Crypt::encryptString($value);
        }

        $settings['secrets'] = $secrets;
        // Re-dispara el mutator para normalizar otras posibles claves
        $this->settings = $settings;
    }

    /**
     * === NUEVO ===
     * Obtiene un valor de settings.
     * Prioriza secretos (settings.secrets.KEY) y claves cifradas legacy KEY_enc; si no, usa en claro.
     */
    public function getSetting(string $key, $default = null)
    {
        // 1) secreto cifrado en settings.secrets.KEY
        $secret = $this->secret($key, null);
        if ($secret !== null) {
            return $secret;
        }

        $settings = (array) ($this->settings ?? []);

        // 2) compatibilidad legacy: KEY_enc en la raíz de settings
        $enc = Arr::get($settings, $key . '_enc');
        if ($enc) {
            try {
                return Crypt::decryptString((string) $enc);
            } catch (\Throwable $e) {
                // si falla, cae al default o al valor en claro
            }
        }

        // 3) valor en claro (no recomendado para secretos, pero útil para otros fields)
        return Arr::get($settings, $key, $default);
    }

    /**
     * === NUEVO ===
     * Establece un valor en settings.
     * - $encrypt = true → guarda en settings.secrets.KEY cifrado y elimina versiones en claro/legacy.
     * - $encrypt = false → guarda en claro y elimina el secreto si existía.
     */
    public function setSetting(string $key, $value, bool $encrypt = false): void
    {
        $settings = (array) ($this->settings ?? []);

        if ($encrypt) {
            // Guardar como secreto
            $this->putSecret($key, $value);

            // Limpiar posibles duplicados en claro o *_enc legacy
            $settings = (array) ($this->settings ?? []);
            Arr::forget($settings, $key);
            Arr::forget($settings, $key . '_enc');
            $this->settings = $settings; // re-dispara mutator
        } else {
            // Guardar en claro
            Arr::set($settings, $key, $value);

            // Limpiar secreto y *_enc legacy
            if (Arr::has($settings, 'secrets.' . $key)) {
                Arr::forget($settings, 'secrets.' . $key);
            }
            Arr::forget($settings, $key . '_enc');

            $this->settings = $settings; // re-dispara mutator
        }
    }
}
