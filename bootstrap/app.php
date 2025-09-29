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
        $middleware->statefulApi();

        // ⚠️ CSRF: agrega comodines para rutas bajo /{locale}/...
        $middleware->validateCsrfTokens(except: [
            '*/api/reviews',
            '*/api/reviews/batch',
            '*/api/apply-promo',
            '*/apply-promo',
        ]);

        // Aliases (opcional)
        $middleware->alias([
            'noindex'          => \App\Http\Middleware\NoIndex::class,
            'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'logctx'           => \App\Http\Middleware\LogContext::class,
            'abilities'        => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability'          => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'normalize.email'  => \App\Http\Middleware\NormalizeEmail::class,
            '2fa.admin'        => \App\Http\Middleware\RequireTwoFactorForAdmins::class,
            'setlocale'        => \App\Http\Middleware\SetLocale::class,
            'locale.redirect'  => \App\Http\Middleware\LocaleRedirect::class,
        ]);

        // ⬇️ ORDEN GLOBAL RECOMENDADO
        // 1) Primero redirigimos a /{locale} o quitamos locale en rutas backend (/login, /two-factor, /admin, etc.)
        $middleware->prepend(\App\Http\Middleware\LocaleRedirect::class);

        // 2) Luego fijamos el locale SIEMPRE (esto inyecta URL::defaults(['locale'=>...]) también en /admin)
        $middleware->append([
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\NormalizeEmail::class,
            \App\Http\Middleware\LogContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Asegura locale en páginas de error (toma 1er segmento si es válido; si no, sesión o default)
        $exceptions->render(function (\Throwable $e, $request) {
            try {
                $supported = (array) config('app.supported_locales', ['es','en','fr','de','pt']);
                $first = explode('/', trim($request->path(), '/'))[0] ?? null;

                $locale = null;
                if ($first && in_array($first, $supported, true)) {
                    $locale = $first;
                } else {
                    $locale = session('locale') ?: config('app.locale','es');
                }

                app()->setLocale($locale);
                \Carbon\Carbon::setLocale($locale);
            } catch (\Throwable $t) {
                // swallow
            }
        });

        // 429 (Throttle)
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            $headers = $e->getHeaders();
            $seconds = (int) ($headers['Retry-After'] ?? 600);

            return response()
                ->view('errors.429', ['seconds' => $seconds], 429)
                ->withHeaders($headers);
        });

        // 413 (payload grande)
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
