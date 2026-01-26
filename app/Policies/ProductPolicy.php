<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        \Log::info('POLICY CHECK: ProductPolicy::viewAny', ['user' => $user->id]);
        return $user->canDo('tours.manage') || $user->canDo('access-admin');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->canDo('tours.manage') || $user->canDo('access-admin');
    }

    public function create(User $user): bool
    {
        return $user->canDo('tours.manage');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->canDo('tours.manage');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->canDo('tours.manage');
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->canDo('tours.manage');
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->canDo('tours.manage');
    }
}
