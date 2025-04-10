<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\UserActivityLogger;

class UserRoleService
{
    /**
     * Create a new UserRoleService instance.
     *
     * @param UserRoleService $roleService The role service for handling permission operations
     */
    public function __construct(private UserActivityLogger $logger)
    {
    }

    /**
     * Check if the current user is authorized to update roles.
     *
     * @return bool Whether the current user is authorized to update roles.
     */
    public function authorize(): bool
    {
        $currentUser = Auth::user();
        if (!($currentUser->is_admin || $currentUser->bac_on)) {
            $this->logger->info('Unauthorized role update attempt', [
                'is_admin' => $currentUser->is_admin,
                'bac_on'   => $currentUser->bac_on
            ]);
            return false;
        }
        return true;
    }

    /**
     * Update a user's role.
     *
     * @param User $user   The user to update
     * @param string $role The role to update
     * @param bool $value  The new value
     * @return array       Result with success status and optional error message
     */
    public function updateRole(User $targetUser, string $role, bool $value): array
    {
        try {
            $targetUser->update([$role => $value]);
            $this->logRoleUpdateAttempt($targetUser->username, $role, $value, null);
            return ['success' => true];
        } catch (\Exception $e) {
            report($e);
            $this->logRoleUpdateAttempt($targetUser->username, $role, $value, $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Log a role update attempt.
     *
     * @param string $targetUsername Username of the user being updated
     * @param string $role           The role being updated
     * @param bool $value            The value the role is being updated to
     * @param ?string $error         An error message
     * @return void
     */
    private function logRoleUpdateAttempt(
        string $targetUsername,
        string $role,
        bool $value,
        ?string $error
    ): void {
        $this->logger->info('Role update attempt', [
            'target_user' => $targetUsername,
            'role'        => $role,
            'value'       => $value,
            'success'     => $error === null,
            'error'       => $error
        ]);
    }
}
