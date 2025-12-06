<?php

namespace App\Policies\Concerns;

trait ChecksAdmin
{
    protected function isAdmin($user): bool
    {
        // Super admins siempre son admins
        if (isset($user->is_super_admin) && $user->is_super_admin) {
            return true;
        }
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }
        // Verificar roles de admin usando Spatie
        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole(['admin', 'super-admin']);
        }
        return false;
    }
}
