<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        // Puedes agregar otras rutas si las usás
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aquí definís tus alias de middleware personalizados
        $middleware->alias([
            'nocliente' => \App\Http\Middleware\NoClientes::class,
            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
