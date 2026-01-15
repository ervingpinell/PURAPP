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
            'webhooks/payment/alignet', // Alignet payment gateway callback
            'payment/return', // Alignet/PayPal return URL (POST/GET)
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
            'throttle.cart'   => \App\Http\Middleware\ThrottleCartActions::class,  // 🆕 Anti-bot
            'alignet.cors'    => \App\Http\Middleware\AlignetCorsMiddleware::class, // 💳 Alignet CORS
            'recaptcha'       => \App\Http\Middleware\VerifyRecaptcha::class,      // 🤖 Anti-bot reCAPTCHA
            // Spatie Permission middleware
            'role'            => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'      => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->append([
            \App\Http\Middleware\ForceCorrectDomain::class,
            \App\Http\Middleware\NormalizeEmail::class,
            \App\Http\Middleware\LogContext::class,
            \App\Http\Middleware\RememberEmail::class,
            \App\Http\Middleware\SecurityHeaders::class,  // 🔒 Security headers (CSP, HSTS)
            \App\Http\Middleware\CacheControl::class,     // ⚡ HTTP cache headers
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SyncCookieConsent::class,
            \App\Http\Middleware\PublicReadOnly::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\PreventCartCaching::class,
            // CSP is handled by SecurityHeaders middleware globally
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            $headers = $e->getHeaders();
            $seconds = (int) ($headers['Retry-After'] ?? 60);
            $minutes = ceil($seconds / 60);

            // Si es una petición AJAX/JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Demasiadas solicitudes. Por favor espera {$minutes} minuto(s) antes de intentar nuevamente.",
                    'retry_after' => $seconds,
                    'retry_after_minutes' => $minutes
                ], 429, $headers);
            }

            // Si es una petición web normal
            return response()
                ->view('errors.429', [
                    'seconds' => $seconds,
                    'minutes' => $minutes
                ], 429)
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

        // 1) Tareas frecuentes (cada 5 min) - Expira carritos vencidos
        $schedule->command('app:check-frequent')
            ->everyFiveMinutes()
            ->name('app:check-frequent')
            ->withoutOverlapping();

        // 2) Tareas Diarias (03:00 AM) - Carritos viejos, overrides, soft deletes
        $schedule->command('app:daily-cleanup')
            ->dailyAt('03:00')
            ->name('app:daily-cleanup')
            ->onOneServer()
            ->withoutOverlapping();

        // 3) Tareas Semanales (Lunes 03:30 AM) - Items huérfanos
        $schedule->command('app:weekly-cleanup')
            ->weeklyOn(1, '03:30')
            ->name('app:weekly-cleanup')
            ->onOneServer();

        // 4) Tareas Mensuales (Día 1 04:00 AM) - Auditoría logs
        $schedule->command('app:monthly-cleanup')
            ->monthlyOn(1, '04:00')
            ->name('app:monthly-cleanup')
            ->onOneServer();

        // 5) Horizon snapshots (infra)
        if (class_exists(\Laravel\Horizon\Horizon::class)) {
            $schedule->command('horizon:snapshot')
                ->everyFiveMinutes()
                ->onOneServer()
                ->name('horizon:snapshot');
        }
    })
    ->create();
