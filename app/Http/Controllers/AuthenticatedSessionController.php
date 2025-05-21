<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    private readonly bool $rateLimitEnabled;

    /**
     * Create a new controller instance and determine whether requests should be rate-limited.
     */
    public function __construct()
    {
        $env = config('app.env');
        $enableLocalRateLimit = config('auth.login_attempts_rate_limit.enable_locally');
        $this->rateLimitEnabled = $env === 'demo' || ($env === 'local' && $enableLocalRateLimit);
    }

    /**
     * Display the login view.
     */
    public function index(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        if ($this->rateLimitEnabled) {
            $request->authenticateOrThrottle();
        } else {
            $request->authenticate();
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
