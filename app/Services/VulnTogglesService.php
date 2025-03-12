<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Http\Requests\VulnTogglesRequest;

class VulnTogglesService
{
    public function updateVulnToggles(VulnTogglesRequest $request)
    {
        $user = auth()->user();
        $toggle = $request->input('toggle');
        $value = $request->input('value') ? true : false;
        if (array_key_exists($toggle, $user->getAttributes())) {
            $user->update([$toggle => $value]);
            $user->save();
            $this->logUpdateAttempt(true, $user->username, $toggle, $value);
            return response()->json(['success' => true]);
        } else {
            $this->logUpdateAttempt(false, $user->username, $toggle, $value);
            return response()->json(['success' => false, 'error' => 'Invalid toggle name'], 422);
        }
    }

    private function logUpdateAttempt($success, $username, $toggleName, $toggleValue)
    {
        $logData = [
            'username' => $username,
            'toggle_name' => $toggleName,
            'toggle_value' => $toggleValue,
        ];

        $logLevel = $success ? 'info' : 'warning';
        $message = $success
            ? 'User updated vulnerability toggle'
            : 'User attempted to update vulnerability toggle';

        Log::channel('user_activity')->{$logLevel}($message, $logData);
    }
}
