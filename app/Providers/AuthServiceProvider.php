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

        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('admin') ? true : null;
        });
        Gate::define('access-admin', fn (User $u) => $u->canDo('access-admin'));
        Gate::define('manage-reviews', fn (User $u) => $u->canDo('manage-reviews'));
        Gate::define('manage-users', fn (User $u) => $u->canDo('manage-users'));

    }
}
