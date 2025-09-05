<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Tus middlewares de la app
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\CheckIfUserLocked;
use App\Http\Middleware\LogContext;
use App\Http\Middleware\NormalizeEmail; // 👈 nuevo
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

// Sanctum (abilities para tokens personales)
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        // commands: __DIR__ . '/../routes/console.php',
        // health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Sanctum: cookies + sesión + CSRF también en /api
        $middleware->statefulApi();

        // Excepciones CSRF para endpoints POST públicos
        $middleware->validateCsrfTokens(except: [
            'api/reviews',
            'api/reviews/batch',
            'api/apply-promo',
        ]);

        // Aliases
        $middleware->alias([
            'CheckRole' => \App\Http\Middleware\CheckRole::class,
            'locked'    => CheckIfUserLocked::class,
            'verified'  => EnsureEmailIsVerified::class,
            'logctx'    => LogContext::class,

            // Sanctum abilities
            'abilities' => CheckAbilities::class,
            'ability'   => CheckForAnyAbility::class,

            // 👇 nuevo
            'normalize.email' => NormalizeEmail::class,
            '2fa.admin' => \App\Http\Middleware\RequireTwoFactorForAdmins::class,

        ]);

        // No forzamos redirect a /login en APIs
        // $middleware->redirectGuestsTo(fn () => route('login'));

        // Orden global (NormalizeEmail primero)
        $middleware->append([
            NormalizeEmail::class, // 👈 primero
            LogContext::class,
            SetLocale::class,
            CheckIfUserLocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
