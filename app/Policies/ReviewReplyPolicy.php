<?php

namespace App\Policies;

use App\Models\ReviewReply;
use App\Models\User;

class ReviewReplyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('view-reviews');
    }

    public function view(User $user, ReviewReply $reply): bool
    {
        return $user->canDo('view-reviews');
    }

    public function create(User $user): bool
    {
        return $user->canDo('reply-reviews');
    }

    public function update(User $user, ReviewReply $reply): bool
    {
        return $user->canDo('moderate-reviews');
    }

    public function delete(User $user, ReviewReply $reply): bool
    {
        return $user->canDo('delete-reviews');
    }
}
