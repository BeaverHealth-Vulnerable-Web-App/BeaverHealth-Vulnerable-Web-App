<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;

class ChangePasswordService
{
    public function updatePassword(User $user, ChangePasswordRequest $request): array
    {
        $usernameConfirmation = $request->input('username_confirmation');
        $currentPasswordInput = $request->input('current_password');
        $newPassword = $request->input('password');
        $hashedNewPassword = Hash::make($newPassword);

        if ($user->sqli_on) {
            $updated = DB::update(
                "UPDATE user SET password = '$hashedNewPassword'
                 WHERE user_id = {$user->user_id}
                 AND (username = '$usernameConfirmation')"
            ) > 0;

            $this->logUpdateAttempt($updated, $user->username, $usernameConfirmation, $user->sqli_on);
            return ['success' => $updated, 'error' => null];
        }

        if ($usernameConfirmation !== $user->username) {
            $this->logUpdateAttempt(false, $user->username, $usernameConfirmation, $user->sqli_on);
            return ['success' => false, 'error' => 'username'];
        }

        if (!Hash::check($currentPasswordInput, $user->password)) {
            $this->logUpdateAttempt(false, $user->username, $usernameConfirmation, $user->sqli_on);
            return ['success' => false, 'error' => 'password'];
        }

        $updated = $user->update([
            'password' => $hashedNewPassword
        ]);

        $this->logUpdateAttempt($updated, $user->username, $usernameConfirmation, $user->sqli_on);

        Log::channel('user_activity')->info('User updated password', [
            'username' => $user->username,
            'current_password' => $currentPasswordInput,
            'new_password' => $newPassword,
        ]);

        return ['success' => $updated, 'error' => null];
    }

    private function logUpdateAttempt($success, $username, $usernameConfirmation, $sqli_enabled)
    {
        $logData = [
            'username' => $username,
            'username_confirmation' => $usernameConfirmation,
            'sqli_enabled' => $sqli_enabled,
        ];

        $logLevel = $success ? 'info' : 'warning';
        $message = $success
            ? 'User updated their password'
            : 'User attempted to update their password';

        Log::channel('user_activity')->{$logLevel}($message, $logData);
    }
}
