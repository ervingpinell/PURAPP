<?php

namespace App\Policies;

use App\Models\Tour;
use App\Models\User;

class TourPolicy
{
    public function viewAny(User $user): bool
    {
        \Log::info('POLICY CHECK: TourPolicy::viewAny', ['user' => $user->id]);
        return $user->canDo('tours.manage') || $user->canDo('access-admin');
    }

    public function view(User $user, Tour $tour): bool
    {
        return $user->canDo('tours.manage') || $user->canDo('access-admin');
    }

    public function create(User $user): bool
    {
        return $user->canDo('tours.manage');
    }

    public function update(User $user, Tour $tour): bool
    {
        return $user->canDo('tours.manage');
    }

    public function delete(User $user, Tour $tour): bool
    {
        return $user->canDo('tours.manage');
    }

    public function restore(User $user, Tour $tour): bool
    {
        return $user->canDo('tours.manage');
    }

    public function forceDelete(User $user, Tour $tour): bool
    {
        return $user->canDo('tours.manage');
    }
}
