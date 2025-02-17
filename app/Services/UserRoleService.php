<?php

namespace App\Services;

use App\Models\User;

class UserRoleService
{
    public function updateRole(User $user, string $role, bool $value): bool
    {
        try {
            $user->update([$role => $value]);
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
