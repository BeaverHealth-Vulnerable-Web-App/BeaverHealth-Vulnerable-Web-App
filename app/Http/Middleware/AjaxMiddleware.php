<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->ajax()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
