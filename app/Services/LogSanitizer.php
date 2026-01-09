<?php

namespace App\Services;

final /**
 * LogSanitizer
 *
 * Handles logsanitizer operations.
 */
class LogSanitizer
{
    /**
     * Lista de claves sensibles que deben redactarse por completo.
     * Se compara en minúsculas y con coincidencia exacta.
     */
    private const SENSITIVE_KEYS = [
        'password', 'password_confirmation',
        'token', 'remember_token',
        'authorization', 'api_key', 'api-token',
        'secret', 'signature',
        'mail_password', 'db_password',
    ];

    /**
     * Enmascara un email manteniendo el primer carácter del usuario y el dominio completo.
     * Ej: "john.doe@example.com" => "j***@example.com"
     */
    public static function maskEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        [$localPart, $domain] = explode('@', $email, 2);
        $maskedLocalPart = mb_substr($localPart, 0, 1) . '***';

        return $maskedLocalPart . '@' . $domain;
    }

    /**
     * Enmascara una cadena dejando visibles los extremos.
     * Ej: "ABCDEFGH" (keepStart=2, keepEnd=2) => "AB****GH"
     */
    public static function maskString(
        string $value,
        int $visibleStartChars = 2,
        int $visibleEndChars = 2
    ): string {
        $length = mb_strlen($value);

        if ($length <= ($visibleStartChars + $visibleEndChars)) {
            return str_repeat('*', $length);
        }

        return mb_substr($value, 0, $visibleStartChars)
            . str_repeat('*', $length - ($visibleStartChars + $visibleEndChars))
            . mb_substr($value, -$visibleEndChars);
    }

    /**
     * Sanitiza arreglos para logs:
     *  - Redacta completamente claves sensibles.
     *  - Trunca strings largos.
     *  - Enmascara emails dentro de strings simples.
     *  - Llama recursivamente si encuentra sub-arreglos.
     *
     * @param array $data              Datos originales.
     * @param int   $maxStringLength   Longitud máxima permitida antes de truncar.
     * @return array                   Datos sanitizados aptos para logging.
     */
    public static function scrubArray(array $data, int $maxStringLength = 120): array
    {
        $sanitizedData = [];

        foreach ($data as $originalKey => $originalValue) {
            $normalizedKey = strtolower((string) $originalKey);

            // 1) Redactar claves sensibles
            if (in_array($normalizedKey, self::SENSITIVE_KEYS, true)) {
                $sanitizedData[$originalKey] = '[REDACTED]';
                continue;
            }

            $sanitizedValue = $originalValue;

            // 2) Transformaciones por tipo
            if (is_string($sanitizedValue)) {
                // 2.a) Truncar si excede el máximo
                if (mb_strlen($sanitizedValue) > $maxStringLength) {
                    $sanitizedValue = mb_substr($sanitizedValue, 0, $maxStringLength) . '…';
                }

                // 2.b) Enmascarar emails simples
                if (str_contains($sanitizedValue, '@') && str_contains($sanitizedValue, '.')) {
                    $sanitizedValue = self::maskEmail($sanitizedValue);
                }
            } elseif (is_array($sanitizedValue)) {
                // 2.c) Recursividad para sub-arreglos
                $sanitizedValue = self::scrubArray($sanitizedValue, $maxStringLength);
            }

            $sanitizedData[$originalKey] = $sanitizedValue;
        }

        return $sanitizedData;
    }
}
