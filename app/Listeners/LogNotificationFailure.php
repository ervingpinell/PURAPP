<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Log;

class LogNotificationFailure
{
    public function handle(NotificationFailed $event): void
    {
        Log::warning('Notificación fallida', [
            'channel'     => $event->channel,        // 'mail'
            'notifiable'  => method_exists($event->notifiable, 'getKey') ? $event->notifiable->getKey() : null,
            'notification'=> get_class($event->notification),
            'data'        => $event->data ?? null,   // puede venir vacío según el canal
        ]);
    }
}
