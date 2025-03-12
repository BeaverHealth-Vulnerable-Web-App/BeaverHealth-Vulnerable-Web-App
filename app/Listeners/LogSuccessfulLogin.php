<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        Log::channel('user_activity')->info('User logged in', [
            'user_id' => $event->user->user_id,
            'username' => $event->user->username,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
