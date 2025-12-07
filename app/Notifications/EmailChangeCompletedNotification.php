<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('mail');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('auth.email_updated_notification.subject'))
            ->greeting(__('auth.email_updated_notification.greeting'))
            ->line(__('auth.email_updated_notification.message', ['email' => $notifiable->email]))
            ->line(__('auth.email_updated_notification.contact_support'))
            ->salutation(__('auth.email_updated_notification.salutation'));
    }
}
