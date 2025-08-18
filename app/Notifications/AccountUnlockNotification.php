<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountUnlockNotification extends Notification
{
    use Queueable;

    public function __construct(public string $unlockUrl) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Traducciones con fallback
        $subject = __('adminlte::auth.account.unlock_mail_subject') ?: 'Desbloqueo de cuenta';
        $intro   = __('adminlte::auth.account.unlock_mail_intro')   ?: 'Recibimos una solicitud para desbloquear tu cuenta.';
        $action  = __('adminlte::auth.account.unlock_mail_action')  ?: 'Desbloquear mi cuenta';
        $outro   = __('adminlte::auth.account.unlock_mail_outro')   ?: 'Si no fuiste tú, puedes ignorar este correo.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('adminlte::adminlte.hello') ?: 'Hola')
            ->line($intro)
            ->action($action, $this->unlockUrl)
            ->line($outro);
    }
}
