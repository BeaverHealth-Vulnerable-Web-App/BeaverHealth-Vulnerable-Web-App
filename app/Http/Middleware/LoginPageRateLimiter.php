<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginPageRateLimiter
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login-page:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response('Too many login page requests. Please try again later.', 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
