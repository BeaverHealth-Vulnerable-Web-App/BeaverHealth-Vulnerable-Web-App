<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page
     *
     * When redirected with a _refresh parameter, perform a second redirect to clean
     * the URL while preserving flash messages. This works with the CheckPermissionMiddleware
     * to prevent access to unauthorized pages via browser back button.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        if ($request->has('_refresh')) {
            if (session()->has('status')) {
                session()->reflash();
            }

            return redirect()->route('dashboard');
        }

        return view('dashboard');
    }
}
