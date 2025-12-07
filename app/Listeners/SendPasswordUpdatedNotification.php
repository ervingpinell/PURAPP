<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\PasswordUpdatedNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPasswordUpdatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        $user = $event->user;

        if ($user instanceof User) {
            try {
                $user->notify(new PasswordUpdatedNotification());
                Log::info('Password update notification sent to user: ' . $user->user_id);
            } catch (\Throwable $e) {
                Log::error('Failed to send password update notification: ' . $e->getMessage());
            }
        }
    }
}
