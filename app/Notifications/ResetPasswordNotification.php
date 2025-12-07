<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as VendorResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends VendorResetPassword
{
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject(__('auth.reset_password.subject'))
            ->greeting(__('auth.reset_password.greeting'))
            ->line(__('auth.reset_password.line1'))
            ->action(__('auth.reset_password.action'), $url)
            ->line(__('auth.reset_password.line2', [
                'count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')
            ]))
            ->line(new \Illuminate\Support\HtmlString(
                '<span style="font-size: 12px; color: #666;">' .
                    __('auth.reset_password.line3') .
                    '</span>'
            ))
            ->salutation(__('auth.reset_password.salutation'));
    }
}
