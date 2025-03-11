<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;

class ChangePasswordService
{
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
            return ['success' => $updated, 'error' => null];
        }

        if ($usernameConfirmation !== $user->username) {
            return ['success' => false, 'error' => 'username'];
        }

        if (!Hash::check($currentPasswordInput, $user->password)) {
            return ['success' => false, 'error' => 'password'];
        }

        $updated = $user->update([
            'password' => $hashedNewPassword
        ]);
        return ['success' => $updated, 'error' => null];
    }
}
