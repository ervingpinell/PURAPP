<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountLockedNotification extends Notification
{
    use Queueable;

    protected string $unlockUrl;

    public function __construct(string $unlockUrl)
    {
        $this->unlockUrl = $unlockUrl;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('adminlte::auth.account.locked_title') ?? 'Tu cuenta ha sido bloqueada')
            ->line(__('adminlte::auth.account.locked_message') ?? 'Has superado el número de intentos permitidos. Por seguridad, tu cuenta fue bloqueada temporalmente.')
            ->action(__('adminlte::auth.account.unlock_mail_action') ?? 'Desbloquear mi cuenta', $this->unlockUrl)
            ->line(__('adminlte::auth.account.unlock_mail_outro') ?? 'Si no fuiste tú, ignora este correo.');
    }
}
