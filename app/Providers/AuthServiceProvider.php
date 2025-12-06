<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

        // Super admins bypass all gates
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
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
