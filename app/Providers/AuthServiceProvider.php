<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Permiso para administradores
        Gate::define('is-admin', function ($user) {
            return $user->role->role_name === 'Admin';
        });

        // Permiso para colaboradores
        Gate::define('is-collaborator', function ($user) {
            return $user->role->role_name === 'Supervisor';
        });

        // Permiso para ambos
        Gate::define('any-user', function ($user) {
            return in_array($user->role->role_name, ['Admin', 'Supervisor']);
        });
    }
}
