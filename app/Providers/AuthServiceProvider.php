<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Tour::class            => \App\Policies\TourPolicy::class,
        \App\Models\Review::class          => \App\Policies\ReviewPolicy::class,
        \App\Models\ReviewReply::class     => \App\Policies\ReviewReplyPolicy::class,
        \App\Models\ReviewProvider::class  => \App\Policies\ReviewProviderPolicy::class,
        \App\Models\ReviewRequest::class   => \App\Policies\ReviewRequestPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Customize Verify Email Notification
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject(Lang::get('Verify Email Address'))
                ->line(Lang::get('Please click the button below to verify your email address.'))
                ->action(Lang::get('Verify Email Address'), $url)
                ->line(new HtmlString(
                    '<span style="font-size: 12px; color: #666;">' .
                        Lang::get('If you did not create an account, no further action is required.') .
                        '</span>'
                ));
        });

        // Super admins bypass all gates
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
            // Admins bypass most gates (except super-admin specific ones)
            if ($user->hasRole('admin')) {
                return true;
            }
            return null;
        });

        // Gates para permisos específicos
        // Gates para permisos específicos
        // Verificamos directamente con hasPermissionTo para evitar recursión
        Gate::define('access-admin', function (User $user) {
            $isSuperOrAdmin = $user->hasRole(['super-admin', 'admin']);
            if ($isSuperOrAdmin) {
                return true;
            }
            return $user->hasPermissionTo('access-admin');
        });

        // Define view-tours gate explicitly
        Gate::define('view-tours', function (User $user) {
            $isSuperOrAdmin = $user->hasRole(['super-admin', 'admin']);
            if ($isSuperOrAdmin) {
                return true;
            }
            return $user->hasPermissionTo('view-tours');
        });
    }
}
