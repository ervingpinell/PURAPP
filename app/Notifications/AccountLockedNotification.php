<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AccountLockedNotification extends Notification
{
    use Queueable;

    public function __construct(public string $unlockUrl) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        Log::info('Sending AccountLockedNotification', [
            'to'   => $notifiable->email,
            'url'  => $this->unlockUrl,
        ]);

        $replyTo = config('mail.reply_to.address');

        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($replyTo)
            ->subject(__('adminlte::auth.account.locked_title') ?: 'Tu cuenta ha sido bloqueada')
            ->greeting(__('adminlte::auth.hello') ?: 'Hola')
            ->line(__('adminlte::auth.account.locked_message') ?: 'Has superado el número de intentos permitidos. Por seguridad, tu cuenta fue bloqueada temporalmente.')
            ->action(__('adminlte::auth.account.unlock_mail_action') ?: 'Desbloquear mi cuenta', $this->unlockUrl)
            ->line(new \Illuminate\Support\HtmlString(
                '<span style="font-size: 12px; color: #666;">' .
                    (__('adminlte::auth.account.unlock_mail_outro') ?: 'Si no fuiste tú, ignora este correo.') .
                    '</span>'
            ));
    }
}
