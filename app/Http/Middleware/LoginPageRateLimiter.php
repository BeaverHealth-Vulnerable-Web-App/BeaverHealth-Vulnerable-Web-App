<?php

namespace App\Http\Middleware;

use App\Services\UserActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginPageRateLimiter
{
    /**
     * Throttles repeated access to the login page based on IP address.
     *
     * @param Request $request The current HTTP request
     * @param Closure $next    The next middleware handler
     *
     * @return Response A 429 response if too many attempts were made,
     *                  or the response from the next middleware/controller
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login-page:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            app(UserActivityLogger::class)->warning(
                'Login page access blocked due to too many requests',
                ['throttle_key' => $key]
            );
            return response('Too many login page requests. Please try again later.', Response::HTTP_TOO_MANY_REQUESTS);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
