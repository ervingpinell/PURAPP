<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Console\Scheduling\Schedule;

// Models usados en las tareas programadas
use App\Models\Cart;
use App\Models\CartItem;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Stack web por defecto (EncryptCookies, AddQueuedCookiesToResponse, StartSession, etc.)
        $middleware->statefulApi();

        // CSRF del core con exclusiones (si tus endpoints usan fetch SIN token)
        // Si ya envías X-CSRF-TOKEN desde el front, puedes QUITAR exclusiones.
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
            \App\Http\Middleware\ForceCorrectDomain::class,
            \App\Http\Middleware\NormalizeEmail::class,
            \App\Http\Middleware\LogContext::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\RememberEmail::class,
        ]);

        // ✅ Aplica middlewares al grupo WEB (después de StartSession)
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SyncCookieConsent::class,
            \App\Http\Middleware\PublicReadOnly::class,  // ✅ ReadOnly en web group
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
    ->withSchedule(function (Schedule $schedule) {
        // 1) Expirar carritos activos vencidos (cada 5 min)
        $schedule->call(function () {
            Cart::query()
                ->where('is_active', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->orderBy('cart_id')
                ->chunkById(500, function ($carts) {
                    foreach ($carts as $cart) {
                        // Limpia items y marca expirado utilizando tu lógica centralizada
                        $cart->forceExpire();
                    }
                });
        })->everyFiveMinutes()->name('carts:expire-overdue')->withoutOverlapping();

        // 2) Purgar carritos inactivos antiguos (p.ej., >7 días) (cada noche)
        $schedule->call(function () {
            $cutoff = now()->subDays(7);

            Cart::query()
                ->where('is_active', false)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', $cutoff)
                ->orderBy('cart_id')
                ->chunkById(500, function ($carts) {
                    foreach ($carts as $cart) {
                        // Por sanidad: borra items y luego el carrito
                        $cart->items()->delete();
                        $cart->delete();
                    }
                });
        })->dailyAt('02:30')->name('carts:prune-old')->onOneServer();

        // 3) Items huérfanos (por si hubo borrados manuales)
        $schedule->call(function () {
            CartItem::query()
                ->whereDoesntHave('cart')
                ->delete();
        })->weeklyOn(1, '03:10')->name('cart_items:prune-orphans')->onOneServer();

        // 4) (Opcional) Horizon snapshots para métricas/tiempos (recomendado)
        // Requiere que Horizon esté instalado y corriendo como daemon.
        if (class_exists(\Laravel\Horizon\Horizon::class)) {
            $schedule->command('horizon:snapshot')
                ->everyFiveMinutes()
                ->onOneServer()
                ->name('horizon:snapshot');
        }
    })
    ->create();
