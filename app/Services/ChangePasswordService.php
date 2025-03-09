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
        $input = $request->input('current_password');
        $newPassword = $request->input('password');

        if ($user->sqli_on) {
            $sql = "UPDATE `user` SET `password` = '" . Hash::make($newPassword)
            . "' WHERE `user_id` = " . $user->user_id . " AND `password` = '" . $input . "'";
            $result = DB::update($sql);
            return $result > 0;
        }

        if (!Hash::check($input, $user->password)) {
            return false;
        }

        return $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }
}
