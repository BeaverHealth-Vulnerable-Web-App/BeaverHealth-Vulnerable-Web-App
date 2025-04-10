<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxMiddleware
{
    /**
     * Ensures the request is an AJAX request
     *
     * @param Request $request The incoming HTTP request
     * @param Closure $next    The next middleware handler
     *
     * @return Response The redirect response if not AJAX, or the response from the next middleware/controller
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->ajax()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
