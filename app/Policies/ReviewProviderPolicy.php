<?php

namespace App\Policies;

use App\Models\ReviewProvider;
use App\Models\User;

class ReviewProviderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('manage-review-providers') || $user->canDo('manage-reviews');
    }

    public function view(User $user, ReviewProvider $provider): bool
    {
        return $user->canDo('manage-review-providers') || $user->canDo('manage-reviews');
    }

    public function create(User $user): bool
    {
        return $user->canDo('manage-review-providers');
    }

    public function update(User $user, ReviewProvider $provider): bool
    {
        return $user->canDo('manage-review-providers');
    }

    public function delete(User $user, ReviewProvider $provider): bool
    {
        return $user->canDo('manage-review-providers');
    }
}
