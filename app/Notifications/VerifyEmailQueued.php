<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;

class VerifyEmailQueued extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];

    /**
     * Se ejecuta cuando el job falla definitivamente.
     */
    public function failed(\Throwable $e): void
    {
        Log::warning('Fallo enviando verificación de email', [
            'notification' => static::class,
            'error' => $e->getMessage(),
            'exception' => get_class($e),
        ]);

        if (strpos($e->getMessage(), '550') !== false) {
            Log::notice('Bounce 550 detectado al verificar email (posible correo inexistente).');
        }
    }
}
