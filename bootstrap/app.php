<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Console\Scheduling\Schedule;

use App\Models\Cart;
use App\Models\CartItem;
use App\Jobs\PurgeOldAvailabilityOverrides;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();

        $middleware->validateCsrfTokens(except: [
            'api/reviews',
            'api/reviews/batch',
            'api/apply-promo',
        ]);

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

        $middleware->append([
            \App\Http\Middleware\ForceCorrectDomain::class,
            \App\Http\Middleware\NormalizeEmail::class,
            \App\Http\Middleware\LogContext::class,
            \App\Http\Middleware\RememberEmail::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SyncCookieConsent::class,
            \App\Http\Middleware\PublicReadOnly::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            $headers = $e->getHeaders();
            $seconds = (int) ($headers['Retry-After'] ?? 600);

            return response()
                ->view('errors.429', ['seconds' => $seconds], 429)
                ->withHeaders($headers);
        });

        $exceptions->render(function (PostTooLargeException $e, $request) {
            $title = app()->bound('translator') ? __('m_tours.image.ui.error_title') : 'Error';
            $text  = app()->bound('translator') ? __('m_tours.image.errors.too_large') : 'The uploaded file is too large.';

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
                        $cart->forceExpire();
                    }
                });
        })->everyFiveMinutes()->name('carts:expire-overdue')->withoutOverlapping();

        // 2) Purgar carritos inactivos antiguos (>7 días) (cada noche)
        $schedule->call(function () {
            $cutoff = now()->subDays(7);

            Cart::query()
                ->where('is_active', false)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', $cutoff)
                ->orderBy('cart_id')
                ->chunkById(500, function ($carts) {
                    foreach ($carts as $cart) {
                        $cart->items()->delete();
                        $cart->delete();
                    }
                });
        })->dailyAt('02:30')->name('carts:prune-old')->onOneServer();

        // 3) Items huérfanos (semanal)
        $schedule->call(function () {
            CartItem::query()
                ->whereDoesntHave('cart')
                ->delete();
        })->weeklyOn(1, '03:10')->name('cart_items:prune-orphans')->onOneServer();

        // 4) Horizon snapshots (opcional)
        if (class_exists(\Laravel\Horizon\Horizon::class)) {
            $schedule->command('horizon:snapshot')
                ->everyFiveMinutes()
                ->onOneServer()
                ->name('horizon:snapshot');
        }

        // 5) Purga de overrides de capacidad (anteriores a HOY)
        //    Usamos call() + dispatch()->onQueue('maintenance') para evitar onQueue() en el Event.
        $schedule->call(function () {
            PurgeOldAvailabilityOverrides::dispatch([
                'daysAgo'      => 0,       // solo fechas estrictamente pasadas
                'onlyInactive' => false,   // true para tocar solo overrides inactivos
                'keepBlocked'  => true,    // conserva bloqueados históricos
                'limit'        => 20000,   // techo de seguridad
                'chunk'        => 1000,
                'dryRun'       => false,   // true para simular
            ])->onQueue('maintenance');
        })
        ->dailyAt('02:40')
        ->name('overrides:purge-old')
        ->onOneServer()
        ->withoutOverlapping();
    })
    ->create();
