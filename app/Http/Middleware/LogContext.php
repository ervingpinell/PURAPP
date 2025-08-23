<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogContext
{
    public const HEADER = 'X-Request-Id';

    private const MAX_UA_LEN   = 180;
    private const MAX_PATH_LEN = 300;
    private const MAX_REF_LEN  = 300;

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        // 1) Request ID entrante o generado
        $requestId = $request->headers->get(self::HEADER) ?: (string) Str::uuid();

        // 2) Datos básicos (sanitizados/recortados)
        $user     = $request->user();
        $userId   = $user?->getAuthIdentifier();

        $path     = '/'.ltrim($request->path(), '/');
        if (mb_strlen($path) > self::MAX_PATH_LEN) {
            $path = mb_substr($path, 0, self::MAX_PATH_LEN).'…';
        }

        $referer  = (string) $request->headers->get('referer', '');
        if ($referer && mb_strlen($referer) > self::MAX_REF_LEN) {
            $referer = mb_substr($referer, 0, self::MAX_REF_LEN).'…';
        }

        $ua       = (string) $request->userAgent();
        if ($ua && mb_strlen($ua) > self::MAX_UA_LEN) {
            $ua = mb_substr($ua, 0, self::MAX_UA_LEN).'…';
        }

        $requestSize = (int) ($request->header('content-length') ?? 0);

        // 3) Contexto global para todos los logs de esta request
        Log::withContext([
            'request_id' => $requestId,
            'user_id'    => $userId,
            'ip'         => $request->ip(),
            'method'     => $request->getMethod(),
            'path'       => $path,
            'route'      => optional($request->route())->getName(),
            'action'     => optional($request->route())->getActionName(),
            'user_agent' => $ua ?: null,
            'referer'    => $referer ?: null,
            'req_size'   => $requestSize ?: null,
        ]);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // 4) Métricas de respuesta
        $durationMs   = (int) round((microtime(true) - $startedAt) * 1000);
        $responseSize = (int) $response->headers->get('Content-Length', 0);

        // Añadimos métricas como contexto adicional (no sobreescribe el contexto anterior)
        Log::withContext([
            'status'   => $response->getStatusCode(),
            'res_size' => $responseSize ?: null,
            'dur_ms'   => $durationMs,
        ]);

        // 5) Cabecera X-Request-Id de salida
        $response->headers->set(self::HEADER, $requestId);

        return $response;
    }
}
