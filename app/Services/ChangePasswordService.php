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

        if ($user->sqli_on) {
            $updated = DB::update(
                "UPDATE user SET password = '$hashedNewPassword'
                 WHERE user_id = {$user->user_id}
                 AND (username = '$usernameConfirmation')"
            ) > 0;

            $this->logPasswordUpdateAttempt($usernameConfirmation, $user->sqli_on, $updated);
            return ['success' => $updated, 'error' => null];
        }

        if ($usernameConfirmation !== $user->username) {
            $this->logPasswordUpdateAttempt($usernameConfirmation, $user->sqli_on, false);
            return ['success' => false, 'error' => 'username'];
        }

        if (!Hash::check($currentPasswordInput, $user->password)) {
            $this->logPasswordUpdateAttempt($usernameConfirmation, $user->sqli_on, false);
            return ['success' => false, 'error' => 'password'];
        }

        $updated = $user->update(['password' => $hashedNewPassword]);
        $this->logPasswordUpdateAttempt($usernameConfirmation, $user->sqli_on, $updated);
        return ['success' => $updated, 'error' => null];
    }

    private function logPasswordUpdateAttempt(string $usernameConfirmation, bool $sqliOn, bool $success): void
    {
        $this->logger->info('Password update attempt', [
            'username_confirmation' => $usernameConfirmation,
            'sqli_on'               => $sqliOn,
            'success'               => $success
        ]);
    }
}
