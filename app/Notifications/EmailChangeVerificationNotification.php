<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class EmailChangeVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Token único para validar el cambio de correo.
     */
    public string $token;

    /**
     * Locale forzado para este correo (opcional).
     * Si viene null, se usará preferred_locale del usuario o app()->getLocale().
     */
    public ?string $localeOverride = null;

    public function __construct(string $token, ?string $locale = null)
    {
        // Misma cola que el resto de correos
        $this->onQueue('mail');

        $this->token          = $token;
        $this->localeOverride = $locale;
    }

    /**
     * Determina el locale del correo (solo 'es' o 'en').
     */
    protected function mailLocale($notifiable): string
    {
        // Prioridad:
        // 1) Locale pasado al constructor
        // 2) preferred_locale del usuario (si existe)
        // 3) app()->getLocale()
        $preferred = null;
        if (isset($notifiable->preferred_locale)) {
            $preferred = $notifiable->preferred_locale;
        }

        $locale = strtolower(
            $this->localeOverride
            ?? $preferred
            ?? app()->getLocale()
        );

        return str_starts_with($locale, 'es') ? 'es' : 'en';
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Forzar el locale antes de construir el MailMessage
        app()->setLocale($this->mailLocale($notifiable));

        // URL firmada temporalmente (ajusta la duración si quieres)
        $url = URL::temporarySignedRoute(
            'email.change.confirm',
            now()->addHours(2),
            [
                'user'  => $notifiable->getKey(),
                'token' => $this->token,
            ]
        );

        return (new MailMessage)
            ->subject(__('auth.email_change_subject'))
            // Usamos tu vista Blade que extiende el layout común de correos
            ->view('emails.auth.email-change', [
                'user' => $notifiable,
                'url'  => $url,
            ]);
    }
}
