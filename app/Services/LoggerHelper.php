<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Facades\Log;

final class LoggerHelper
{
    private static function base(
        string $controller,
        string $action,
        string $message,
        array $context = []
    ): array {
        // Claves estándar al inicio
        $std = [
            'controller' => $controller,
            'action'     => $action,
            'entity'     => $context['entity']    ?? null,
            'entity_id'  => $context['entity_id'] ?? null,
            'user_id'    => $context['user_id']   ?? null,
        ];

        // Evitar duplicados en el contexto
        unset($context['entity'], $context['entity_id'], $context['user_id'], $context['request_id']);

        // Sanitizar el resto
        $scrubbed = LogSanitizer::scrubArray($context);

        return array_filter(
            array_merge($std, $scrubbed),
            static fn($v) => $v !== null && $v !== ''
        );
    }

    public static function info(
        string $controller,
        string $action,
        string $message,
        array $context = []
    ): void {
        Log::info($message, self::base($controller, $action, $message, $context));
    }

    public static function warning(
        string $controller,
        string $action,
        string $message,
        array $context = []
    ): void {
        Log::warning($message, self::base($controller, $action, $message, $context));
    }

    public static function error(
        string $controller,
        string $action,
        string $message,
        array $context = [],
        ?Throwable $e = null
    ): void {
        if ($e) {
            $context['error'] = $e->getMessage();
            $context['trace'] = $e->getTraceAsString();
        }
        Log::error($message, self::base($controller, $action, $message, $context));
    }

    /* Atajos semánticos */

    public static function validationFailed(
        string $controller,
        string $action,
        array $errors,
        array $ctx = []
    ): void {
        self::warning($controller, $action, 'Validation failed', array_merge($ctx, [
            'errors' => $errors,
        ]));
    }

    public static function mutated(
        string $controller,
        string $action,
        string $entity,
        $entityId,
        array $ctx = []
    ): void {
        self::info($controller, $action, 'Mutation OK', array_merge($ctx, [
            'entity'    => $entity,
            'entity_id' => $entityId,
        ]));
    }

    public static function exception(
        string $controller,
        string $action,
        string $entity,
        $entityId,
        Throwable $e,
        array $ctx = []
    ): void {
        self::error($controller, $action, 'Mutation FAILED', array_merge($ctx, [
            'entity'    => $entity,
            'entity_id' => $entityId,
        ]), $e);
    }
}
