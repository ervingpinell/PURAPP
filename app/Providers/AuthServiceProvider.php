<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Policies por modelo (agrega aquí tus policies cuando las vayas creando).
     */
    protected $policies = [
    \App\Models\Tour::class => \App\Policies\TourPolicy::class,
    \App\Models\Review::class          => \App\Policies\ReviewPolicy::class,
    \App\Models\ReviewReply::class     => \App\Policies\ReviewReplyPolicy::class,
    \App\Models\ReviewProvider::class  => \App\Policies\ReviewProviderPolicy::class,
    \App\Models\ReviewRequest::class   => \App\Policies\ReviewRequestPolicy::class,
    ];

    public function boot(): void
    {
        // Acceso al panel admin (Admin + Supervisor)
        Gate::define('access-admin', function (User $user) {
            // Evitamos N+1 usando role_id directamente
            return in_array((int) $user->role_id, [1, 2], true);
        });

        // Solo administradores
        Gate::define('manage-users', fn (User $user) => (int) $user->role_id === 1);

        // Compatibilidad con lo que tenías (opcional)
        Gate::define('is-admin', fn (User $user) => (int) $user->role_id === 1);
        Gate::define('is-collaborator', fn (User $user) => (int) $user->role_id === 2);
        Gate::define('any-user', fn (User $user) => in_array((int) $user->role_id, [1, 2], true));
    }
}
