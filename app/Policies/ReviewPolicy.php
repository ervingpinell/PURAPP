<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('view-reviews');
    }

    public function view(User $user, Review $review): bool
    {
        return $user->canDo('view-reviews');
    }

    public function create(User $user): bool
    {
        return $user->canDo('create-reviews');
    }

    public function update(User $user, Review $review): bool
    {
        return $user->canDo('moderate-reviews');
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->canDo('delete-reviews');
    }

    public function restore(User $user, Review $review): bool
    {
        return $user->canDo('delete-reviews');
    }

    public function forceDelete(User $user, Review $review): bool
    {
        return $user->canDo('delete-reviews');
    }
}
