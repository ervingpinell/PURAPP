<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Usa el stack web por defecto de Laravel (EncryptCookies, AddQueuedCookiesToResponse,
        // StartSession, ShareErrorsFromSession, VerifyCsrfToken, SubstituteBindings, etc.)
        // No declares tu propio VerifyCsrfToken de App\... (¡no es necesario en L11!).

        // Si usas Sanctum con SPA/API stateful:
        $middleware->statefulApi();

        // CSRF del core con exclusiones:
        $middleware->validateCsrfTokens(except: [
            'api/reviews',
            'api/reviews/batch',
            'api/apply-promo',
        ]);

        // Aliases de middleware (solo los que realmente uses en rutas)
        $middleware->alias([
            'noindex'         => \App\Http\Middleware\NoIndex::class,
            'verified'        => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'logctx'          => \App\Http\Middleware\LogContext::class,
            'abilities'       => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability'         => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'normalize.email' => \App\Http\Middleware\NormalizeEmail::class,
            '2fa.admin'       => \App\Http\Middleware\RequireTwoFactorForAdmins::class,
        ]);

        // Globales (se agregan al final de la pila global; no reemplazan el stack web nativo)
        $middleware->append([
            \App\Http\Middleware\NormalizeEmail::class,
            \App\Http\Middleware\LogContext::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Asegura locale también en errores
        $exceptions->render(function (\Throwable $e, $request) {
            app()->setLocale(session('locale', config('app.locale')));
        });

        // 429: Too Many Requests
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            $headers = $e->getHeaders();
            $seconds = (int) ($headers['Retry-After'] ?? 600);

            return response()
                ->view('errors.429', ['seconds' => $seconds], 429)
                ->withHeaders($headers);
        });

        // 413: Payload Too Large
        $exceptions->render(function (PostTooLargeException $e, $request) {
            $title = __('m_tours.image.ui.error_title');
            $text  = __('m_tours.image.errors.too_large');

            if ($request->wantsJson()) {
                return response()->json([
                    'ok'   => false,
                    'swal' => [
                        'icon'  => 'error',
                        'title' => $title,
                        'text'  => $text,
                    ],
                ], 413);
            }

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => $title,
                'text'  => $text,
            ]);
        });
    })
    ->create();
