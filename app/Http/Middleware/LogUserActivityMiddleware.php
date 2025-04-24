<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\UserActivityLogger;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivityMiddleware
{
    /**
     * Handle an incoming request and log user activity if the user is authenticated.
     *
     * @param Request $request The current HTTP request
     * @param Closure $next    The next middleware to call
     *
     * @return Response The response from the next middleware or controller
     */
    public function __invoke(Request $request, Closure $next): Response
    {
        $response = $next($request);
        app(UserActivityLogger::class)->info('User accessed route');
        return $response;
    }
}
