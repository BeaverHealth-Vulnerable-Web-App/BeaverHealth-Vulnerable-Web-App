<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;

class ChangePasswordService
{
    public function updatePassword(User $user, ChangePasswordRequest $request): bool
    {
        $input = $request->input('current_password');

        if ($user->sqli_on && strpos($input, "' OR '1'='1") !== false) {
            session()->flash('sql_injection_alert', 'SQL Injection Successful!');
            return $user->update([
                'password' => Hash::make($request->input('password'))
            ]);
        }

        // Normal processing: verify the current password.
        if (!Hash::check($input, $user->password)) {
            return false;
        }

        return $user->update([
            'password' => Hash::make($request->input('password'))
        ]);
    }
}
