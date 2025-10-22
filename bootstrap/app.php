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
        // Stack web por defecto (EncryptCookies, AddQueuedCookiesToResponse, StartSession, etc.)
        $middleware->statefulApi();

        // CSRF del core con exclusiones
        $middleware->validateCsrfTokens(except: [
            'api/reviews',
            'api/reviews/batch',
            'api/apply-promo',
        ]);

        // Aliases
        $middleware->alias([
            'noindex'         => \App\Http\Middleware\NoIndex::class,
            'verified'        => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'logctx'          => \App\Http\Middleware\LogContext::class,
            'abilities'       => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability'         => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'normalize.email' => \App\Http\Middleware\NormalizeEmail::class,
            '2fa.admin'       => \App\Http\Middleware\RequireTwoFactorForAdmins::class,
            'public.readonly' => \App\Http\Middleware\PublicReadOnly::class,
        ]);

        // Globales (corren antes del grupo web)
        $middleware->append([
            \App\Http\Middleware\ForceCorrectDomain::class,  // ✅ NUEVO: Fuerza dominio correcto
            \App\Http\Middleware\NormalizeEmail::class,
            \App\Http\Middleware\LogContext::class,
            \App\Http\Middleware\SetLocale::class,

            // Recordar email en el login Fortify (no requiere sesión)
            \App\Http\Middleware\RememberEmail::class,
            // ⛔️ Quitamos aquí SyncCookieConsent (no debe ir global)
        ]);

        // ✅ SyncCookieConsent debe ir en el GRUPO WEB (después de StartSession)
        $middleware->appendToGroup('web', \App\Http\Middleware\SyncCookieConsent::class);
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
