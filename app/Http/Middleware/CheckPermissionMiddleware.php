<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserActivityLogger;

class CheckPermissionMiddleware
{
    private const ROUTE_PERMISSION_MAP = [
        'admin'           => 'is_admin',
        'records.request' => 'request_records',
        'records.add'     => 'load_records',
        'patients.index'  => 'view_patient_info',
        'patients.info'   => 'view_patient_info',
    ];

    /**
    * Enforces role-based access control and handles BAC vulnerability toggling.
    *
    * @param Request $request The current HTTP request
    * @param Closure $next    The next middleware to call
    * @return mixed
    */
    public function __invoke(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        $routeName = $request->route()->getName();
        $isProtectedRoute = array_key_exists($routeName, self::ROUTE_PERMISSION_MAP);

        if (!$user->bac_on && $isProtectedRoute) {
            $requiredPermission = self::ROUTE_PERMISSION_MAP[$routeName];
            if (!$user->{$requiredPermission}) {
                app(UserActivityLogger::class)->info('Route access denied', [
                    'route_name'  => $routeName,
                    'bac_enabled' => $user->bac_on
                ]);
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
            // Prevent browser from using cached pages when using back button
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
