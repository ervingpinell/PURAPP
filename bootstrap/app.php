<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\CheckIfUserLocked;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\LogContext;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'CheckRole' => \App\Http\Middleware\CheckRole::class,
            'locked'    => CheckIfUserLocked::class,
            'verified'  => EnsureEmailIsVerified::class,
            'logctx'    => LogContext::class,
        ]);


        $middleware->append([
            LogContext::class,
            SetLocale::class,
            CheckIfUserLocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })
    ->create();
