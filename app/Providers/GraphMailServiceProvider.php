<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use App\Services\Mail\GraphAuthService;
use App\Services\Mail\GraphMailTransport;

class GraphMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar el servicio de autenticación como singleton
        $this->app->singleton(GraphAuthService::class, function ($app) {
            return new GraphAuthService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Extender el Mail Manager con el driver 'graph'
        Mail::extend('graph', function ($config) {
            $authService = $this->app->make(GraphAuthService::class);

            $senderUpn = config('services.microsoft.sender_upn');
            $replyTo   = config('services.microsoft.reply_to');

            if (!$senderUpn) {
                throw new \RuntimeException('MSFT_SENDER_UPN no está configurado en .env');
            }

            return new GraphMailTransport($authService, $senderUpn, $replyTo);
        });
    }
}
