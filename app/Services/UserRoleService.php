<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
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
        if (!($currentUser->is_admin || $currentUser->bac_on)) {
            Log::channel('user_activity')->info('Unauthorized user attempted to update a role', [
                'username' => $currentUser->username
            ]);
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
    public function updateRole(User $currentUser, User $targetUser, string $role, bool $value): array
    {
        try {
            $targetUser->update([$role => $value]);
            $this->logUpdateAttempt(true, $currentUser->username, $targetUser->username, $role, $value);
            return ['success' => true];
        } catch (\Exception $e) {
            report($e);
            $this->logUpdateAttempt(false, $currentUser->username, $targetUser->username, $role, $value);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function logUpdateAttempt($success, $username, $targetUsername, $role, $value)
    {
        $logData = [
            'username' => $username,
            'target_username' => $targetUsername,
            'role' => $role,
            'value' => $value,
        ];

        $logLevel = $success ? 'info' : 'warning';
        $message = $success
            ? 'User updated a role'
            : 'User attempted to update a role';

        Log::channel('user_activity')->{$logLevel}($message, $logData);
    }
}
