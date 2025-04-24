<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;
use App\Services\UserActivityLogger;

class ChangePasswordService
{
    public function __construct(private UserActivityLogger $logger)
    {
    }

    public function updatePassword(User $user, ChangePasswordRequest $request): array
    {
        $usernameConfirmation = $request->input('username_confirmation');
        $currentPasswordInput = $request->input('current_password');
        $hashedNewPassword = Hash::make($request->input('password'));
        $status = null;

        if ($user->sqli_on) {
            $updated = DB::update(
                "UPDATE user SET password = '$hashedNewPassword'
                 WHERE user_id = {$user->user_id}
                 AND (username = '$usernameConfirmation')"
            ) > 0;
            $status = ['success' => $updated, 'error' => null];
        } else {
            if ($usernameConfirmation !== $user->username) {
                $status = ['success' => false, 'error' => 'username'];
            } elseif (!Hash::check($currentPasswordInput, $user->password)) {
                $status = ['success' => false, 'error' => 'password'];
            }
        }

        if ($status === null) {
            $updated = $user->update([
                'password' => $hashedNewPassword
            ]);

            $status = ['success' => $updated, 'error' => null];
        }

        $this->logger->info('Password update attempt', [
            'username_confirmation' => $usernameConfirmation,
            'sqli_on'               => $user->sqli_on,
            'success'               => $status['success'],
            'failure_reason'        => $status['error']
        ]);

        return $status;
    }
}
