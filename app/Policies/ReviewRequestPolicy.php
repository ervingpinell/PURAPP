<?php

namespace App\Policies;

use App\Models\ReviewRequest;
use App\Models\User;

class ReviewRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('manage-review-requests') || $user->canDo('manage-reviews');
    }

    public function view(User $user, ReviewRequest $request): bool
    {
        return $user->canDo('manage-review-requests') || $user->canDo('manage-reviews');
    }

    public function create(User $user): bool
    {
        return $user->canDo('manage-review-requests');
    }

    public function update(User $user, ReviewRequest $request): bool
    {
        return $user->canDo('manage-review-requests');
    }

    public function delete(User $user, ReviewRequest $request): bool
    {
        return $user->canDo('manage-review-requests');
    }
}
