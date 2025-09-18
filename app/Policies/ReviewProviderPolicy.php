<?php

namespace App\Policies;

use App\Models\ReviewProvider;
use App\Models\User;
use App\Policies\Concerns\ChecksAdmin;

class ReviewProviderPolicy
{
    use ChecksAdmin;

    public function viewAny(User $user): bool { return $this->isAdmin($user); }
    public function create(User $user): bool  { return $this->isAdmin($user); }
    public function update(User $user, ReviewProvider $p): bool { return $this->isAdmin($user); }
    public function delete(User $user, ReviewProvider $p): bool { return $this->isAdmin($user); }
}
