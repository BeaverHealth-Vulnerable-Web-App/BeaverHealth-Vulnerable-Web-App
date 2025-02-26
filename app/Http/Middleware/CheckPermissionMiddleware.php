<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user->idor_on) {
            return $next($request);
        }

        $routePermissionMap = [
            'admin' => 'is_admin',
            'records.request' => 'request_records',
            'records.add' => 'load_records',
            'patients.index' => 'view_patient_info',
            'patients.info' => 'view_patient_info',
        ];

        $routeName = $request->route()->getName();

        if (array_key_exists($routeName, $routePermissionMap)) {
            $requiredPermission = $routePermissionMap[$routeName];

            if (!$user->{$requiredPermission}) {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }
}
