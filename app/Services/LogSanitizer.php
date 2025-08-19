<?php

namespace App\Services;

final class LogSanitizer
{
    /**
     * Campos sensibles que jamás deben loguearse en claro
     */
    private const SENSITIVE_KEYS = [
        'password', 'password_confirmation',
        'token', 'remember_token',
        'authorization', 'api_key', 'api-token',
        'secret', 'signature',
        'mail_password', 'db_password',
    ];

    /**
     * Enmascarar email (ejemplo: a***@example.com)
     */
    public static function maskEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        [$user, $domain] = explode('@', $email, 2);
        $userMasked = mb_substr($user, 0, 1) . '***';

        return $userMasked . '@' . $domain;
    }

    /**
     * Enmascarar string genérico manteniendo n caracteres al inicio y al final
     */
    public static function maskString(string $value, int $keepStart = 2, int $keepEnd = 2): string
    {
        $len = mb_strlen($value);

        if ($len <= ($keepStart + $keepEnd)) {
            return str_repeat('*', $len);
        }

        return mb_substr($value, 0, $keepStart)
            . str_repeat('*', $len - ($keepStart + $keepEnd))
            . mb_substr($value, -$keepEnd);
    }

    /**
     * Sanitizar arrays (recursivo).
     * - Oculta campos sensibles.
     * - Recorta strings largos.
     * - Enmascara emails.
     */
    public static function scrubArray(array $data, int $maxStringLen = 120): array
    {
        $clean = [];

        foreach ($data as $k => $v) {
            $key = strtolower((string) $k);

            if (in_array($key, self::SENSITIVE_KEYS, true)) {
                $clean[$k] = '[REDACTED]';
                continue;
            }

            if (is_string($v)) {
                // Recortar strings largos
                $v = mb_strlen($v) > $maxStringLen
                    ? mb_substr($v, 0, $maxStringLen) . '…'
                    : $v;

                // Heurística: si parece email, enmascarar
                if (str_contains($v, '@') && str_contains($v, '.')) {
                    $v = self::maskEmail($v);
                }
            } elseif (is_array($v)) {
                $v = self::scrubArray($v, $maxStringLen);
            }

            $clean[$k] = $v;
        }

        return $clean;
    }
}
