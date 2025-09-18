<?php

namespace App\Policies\Concerns;

trait ChecksAdmin
{
    protected function isAdmin($user): bool
    {
        if (isset($user->is_admin) && $user->is_admin) return true;
        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole(['Admin','Supervisor']);
        }
        return false;
    }
}
