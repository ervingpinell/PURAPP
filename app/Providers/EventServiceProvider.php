<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationFailed;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NotificationFailed::class => [
            \App\Listeners\LogNotificationFailure::class,
        ],
        \Illuminate\Auth\Events\PasswordReset::class => [
            \App\Listeners\SendPasswordUpdatedNotification::class,
        ],
    ];

    protected $subscribe = [
        \App\Listeners\AuthAuditSubscriber::class,
    ];
}
