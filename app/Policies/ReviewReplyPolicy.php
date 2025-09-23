<?php

namespace App\Policies;

use App\Models\ReviewReply;
use App\Models\User;

class ReviewReplyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('manage-reviews');
    }

    public function view(User $user, ReviewReply $reply): bool
    {
        return $user->canDo('manage-reviews');
    }

    public function create(User $user): bool
    {
        return $user->canDo('manage-reviews');
    }

    public function update(User $user, ReviewReply $reply): bool
    {
        return $user->canDo('manage-reviews');
    }

    public function delete(User $user, ReviewReply $reply): bool
    {
        return $user->canDo('manage-reviews');
    }
}
