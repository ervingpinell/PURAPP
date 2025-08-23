<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Facades\Log;

final class LoggerHelper
{
    private static function buildLogContext(
        string $controllerName,
        string $actionName,
        string $logMessage,
        array $extraContext = []
    ): array {
        $baseContext = [
            'controller' => $controllerName,
            'action'     => $actionName,
            'entity'     => $extraContext['entity']    ?? null,
            'entity_id'  => $extraContext['entity_id'] ?? null,
            'user_id'    => $extraContext['user_id']   ?? null,
        ];

        // Evitamos duplicar datos ya normalizados
        unset(
            $extraContext['entity'],
            $extraContext['entity_id'],
            $extraContext['user_id'],
            $extraContext['request_id']
        );

        // Limpiar información sensible
        $sanitizedContext = LogSanitizer::scrubArray($extraContext);

        // Filtramos valores nulos o vacíos
        return array_filter(
            array_merge($baseContext, $sanitizedContext),
            static fn($value) => $value !== null && $value !== ''
        );
    }

    public static function info(
        string $controllerName,
        string $actionName,
        string $logMessage,
        array $extraContext = []
    ): void {
        Log::info($logMessage, self::buildLogContext($controllerName, $actionName, $logMessage, $extraContext));
    }

    public static function warning(
        string $controllerName,
        string $actionName,
        string $logMessage,
        array $extraContext = []
    ): void {
        Log::warning($logMessage, self::buildLogContext($controllerName, $actionName, $logMessage, $extraContext));
    }

    public static function error(
        string $controllerName,
        string $actionName,
        string $logMessage,
        array $extraContext = [],
        ?Throwable $exception = null
    ): void {
        if ($exception) {
            $extraContext['error_message'] = $exception->getMessage();
            $extraContext['stack_trace']   = $exception->getTraceAsString();
        }

        Log::error($logMessage, self::buildLogContext($controllerName, $actionName, $logMessage, $extraContext));
    }

    public static function validationFailed(
        string $controllerName,
        string $actionName,
        array $validationErrors,
        array $extraContext = []
    ): void {
        self::warning(
            $controllerName,
            $actionName,
            'Validation failed',
            array_merge($extraContext, [
                'validation_errors' => $validationErrors,
            ])
        );
    }

    public static function mutated(
        string $controllerName,
        string $actionName,
        string $entityName,
        $entityId,
        array $extraContext = []
    ): void {
        self::info(
            $controllerName,
            $actionName,
            'Mutation successful',
            array_merge($extraContext, [
                'entity'    => $entityName,
                'entity_id' => $entityId,
            ])
        );
    }

    public static function exception(
        string $controllerName,
        string $actionName,
        string $entityName,
        $entityId,
        Throwable $exception,
        array $extraContext = []
    ): void {
        self::error(
            $controllerName,
            $actionName,
            'Mutation failed',
            array_merge($extraContext, [
                'entity'    => $entityName,
                'entity_id' => $entityId,
            ]),
            $exception
        );
    }
}
