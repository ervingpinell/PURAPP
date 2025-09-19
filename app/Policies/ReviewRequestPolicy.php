<?php

namespace App\Policies;

use App\Models\ReviewRequest;
use App\Models\User;
use App\Policies\Concerns\ChecksAdmin;

class ReviewRequestPolicy
{
    use ChecksAdmin;

    public function viewAny(User $user): bool { return $this->isAdmin($user); }
    public function create(User $user): bool  { return $this->isAdmin($user); }
    public function update(User $user, ReviewRequest $r): bool { return $this->isAdmin($user); }
    public function delete(User $user, ReviewRequest $r): bool { return $this->isAdmin($user); }
}
