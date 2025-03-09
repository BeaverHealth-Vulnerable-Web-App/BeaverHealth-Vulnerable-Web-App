<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;

class ChangePasswordService
{
    public function updatePassword(User $user, ChangePasswordRequest $request): bool
    {
        $usernameConfirmation = $request->input('username_confirmation');
        $currentPasswordInput = $request->input('current_password');
        $newPassword = $request->input('password');

        if ($user->sqli_on) {
            $hashedNewPassword = Hash::make($newPassword);
            return DB::update(
                "UPDATE user SET password = '$hashedNewPassword'
                 WHERE user_id = {$user->user_id}
                 AND (username = '$usernameConfirmation')"
            ) > 0;
        }

        if ($usernameConfirmation !== $user->username || !Hash::check($currentPasswordInput, $user->password)) {
            return false;
        }

        return $user->update(['password' => Hash::make($newPassword)]);
    }
}
