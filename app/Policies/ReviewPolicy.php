<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Policies\Concerns\ChecksAdmin;

class ReviewPolicy
{
    use ChecksAdmin;

    public function viewAny(?User $user): bool
    {
        return true; // listado público (filtrado por published/is_public)
    }

    public function view(?User $user, Review $review): bool
    {
        return $review->status === 'published' && $review->is_public;
    }

    public function create(?User $user): bool
    {
        // clientes (con/sin login) pueden crear; protege con captcha/throttle en FormRequest/Controller
        return true;
    }

    public function update(User $user, Review $review): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Review $review): bool
    {
        return $this->isAdmin($user);
    }

    public function moderate(User $user, Review $review): bool
    {
        return $this->isAdmin($user);
    }

    public function reply(User $user, Review $review): bool
    {
        return $this->isAdmin($user);
    }
}
