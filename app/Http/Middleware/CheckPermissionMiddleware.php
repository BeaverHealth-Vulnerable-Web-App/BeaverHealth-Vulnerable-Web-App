<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissionMiddleware
{
    /**
    * Enforces role-based access control and handles IDOR vulnerability toggling.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $routePermissionMap = [
            'admin' => 'is_admin',
            'records.request' => 'request_records',
            'records.add' => 'load_records',
            'patients.index' => 'view_patient_info',
            'patients.info' => 'view_patient_info',
        ];

        $routeName = $request->route()->getName();
        $isProtectedRoute = array_key_exists($routeName, $routePermissionMap);

        if (!$user->idor_on && $isProtectedRoute) {
            $requiredPermission = $routePermissionMap[$routeName];
            if (!$user->{$requiredPermission}) {
                session()->flash('access-status', [
                    'type' => 'error',
                    'message' => 'Access denied: You do not have permission to view this page.'
                ]);

                // Add timestamp parameter to force refresh on browser back button
                return redirect()->route('dashboard', ['_refresh' => time()]);
            }
        }
        $response = $next($request);

        if ($isProtectedRoute) {
            // Prevents browser from using cached pages when using back button
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
