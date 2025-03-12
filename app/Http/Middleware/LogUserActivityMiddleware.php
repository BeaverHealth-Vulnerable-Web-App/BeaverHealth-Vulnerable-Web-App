<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogUserActivityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            $path = $request->path();
            $method = $request->method();
            $ip = $request->ip();

            Log::channel('user_activity')->info("User activity", [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'path' => $path,
                'method' => $method,
                'ip' => $ip,
                'user_agent' => $request->userAgent()
            ]);
        }

        return $response;
    }
}
