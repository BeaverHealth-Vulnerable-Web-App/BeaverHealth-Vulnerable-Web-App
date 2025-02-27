<?php

namespace App\Services;

use App\Models\User;

class UserRoleService
{
    /**
     * Check if the current user is authorized to update roles.
     *
     * @return bool Whether the current user is authorized to update roles.
     */
    public function authorize(): bool
    {
        $currentUser = auth()->user();
        if (!($currentUser->is_admin || $currentUser->idor_on)) {
            return false;
        }
        return true;
    }

    /**
     * Update user role
     *
     * @param User $user The user to update
     * @param string $role The role to update
     * @param bool $value The new value
     * @return array Result with success status and optional error message
     */
    public function updateRole(User $user, string $role, bool $value): array
    {
        try {
            $user->update([$role => $value]);
            return ['success' => true];
        } catch (\Exception $e) {
            report($e);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
