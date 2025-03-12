<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogout
{
    public function handle(Logout $event)
    {
        Log::channel('user_activity')->info('User logged out', [
            'user_id' => $event->user->user_id,
            'username' => $event->user->username,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
