<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordUpdatedNotification extends Notification implements ShouldQueue
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
        $loginUrl = route('login');

        // Force locale if preferredLocale is present
        if (method_exists($notifiable, 'preferredLocale')) {
            app()->setLocale($notifiable->preferredLocale());
        }

        return (new MailMessage)
            ->subject(__('auth.password_updated_notification.subject'))
            ->greeting(__('auth.password_updated_notification.greeting'))
            ->line(__('auth.password_updated_notification.line1'))
            ->action(__('auth.password_updated_notification.action'), $loginUrl)
            ->line(__('auth.password_updated_notification.line2'))
            ->salutation(__('auth.password_updated_notification.salutation'));
    }
}
