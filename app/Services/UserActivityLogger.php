<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserActivityLogger
{
    /**
     * Logs an info-level message with contextual user and request information.
     *
     * @param string $message The message to log
     * @param array  $context Additional context to include in the log entry
     *
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log($message, $context, 'info');
    }

    /**
     * Logs a warning-level message with contextual user and request information.
     *
     * @param string $message The message to log
     * @param array  $context Additional context to include in the log entry
     *
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log($message, $context, 'warning');
    }

    /**
     * Logs a message with contextual user and request information.
     *
     * @param string $message The message to log
     * @param array  $context Additional context to include in the log entry
     * @param string $level   The log level (e.g., 'info', 'warning', 'error')
     *
     * @return void
     */
    private function log(string $message, array $context = [], string $level = 'info'): void
    {
        $user = Auth::user();
        $request = request();

        $baseContext = [
            'user_id'    => $user?->user_id,
            'username'   => $user?->username,
            'ip'         => $request->ip(),
            'path'       => $request->path(),
            'method'     => $request->method(),
            'user_agent' => $request->userAgent(),
        ];

        Log::channel('user_activity')->{$level}($message, array_merge($baseContext, $context));
    }
}
