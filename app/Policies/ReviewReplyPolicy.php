<?php

namespace App\Policies;

use App\Models\ReviewReply;
use App\Models\User;
use App\Policies\Concerns\ChecksAdmin;

class ReviewReplyPolicy
{
    use ChecksAdmin;

    public function create(User $user): bool  { return $this->isAdmin($user); }
    public function update(User $user, ReviewReply $reply): bool { return $this->isAdmin($user); }
    public function delete(User $user, ReviewReply $reply): bool { return $this->isAdmin($user); }
}
