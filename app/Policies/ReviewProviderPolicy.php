<?php

namespace App\Policies;

use App\Models\ReviewProvider;
use App\Models\User;

class ReviewProviderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('view-review-providers');
    }

    public function view(User $user, ReviewProvider $provider): bool
    {
        return $user->canDo('view-review-providers');
    }

    public function create(User $user): bool
    {
        return $user->canDo('create-review-providers');
    }

    public function update(User $user, ReviewProvider $provider): bool
    {
        return $user->canDo('edit-review-providers');
    }

    public function delete(User $user, ReviewProvider $provider): bool
    {
        return $user->canDo('delete-review-providers');
    }
}
