<?php

namespace App\Policies;

use App\Models\ReviewRequest;
use App\Models\User;

class ReviewRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('view-review-requests');
    }

    public function view(User $user, ReviewRequest $request): bool
    {
        return $user->canDo('view-review-requests');
    }

    public function create(User $user): bool
    {
        return $user->canDo('create-review-requests');
    }

    public function update(User $user, ReviewRequest $request): bool
    {
        return $user->canDo('edit-review-requests');
    }

    public function delete(User $user, ReviewRequest $request): bool
    {
        return $user->canDo('delete-review-requests');
    }
}
