<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;

class ChangePasswordService
{
    public function updatePassword(User $user, ChangePasswordRequest $request): bool
    {
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return false;
        }

        return $user->update([
            'password' => Hash::make($request->input('password'))
        ]);
    }
}
