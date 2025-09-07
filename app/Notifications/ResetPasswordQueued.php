<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class ResetPasswordQueued extends Notification implements ShouldQueue
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->onQueue('mail'); // opcional
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        Log::info('Enviando reset password', ['to' => $notifiable->email, 'url' => $url]);

        return (new MailMessage)
            ->subject(__('adminlte::auth.reset_password'))
            ->line(__('adminlte::auth.password_reset_message'))
            ->action(__('adminlte::auth.reset_password'), $url)
            ->line(__('adminlte::auth.verify.browser_hint', [
                'action' => __('adminlte::auth.reset_password'),
                'url'    => $url,
            ]));
    }

    public function failed(\Throwable $e): void
    {
        Log::warning('Fallo al enviar email de reset password', ['error' => $e->getMessage()]);
        if (str_contains($e->getMessage(), '550')) {
            Log::notice('Bounce 550 detectado al enviar reset password (posible correo inexistente).');
        }
    }
}
