<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
            'username' => ['required', 'string', 'max:255', 'unique:user'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]
        );

        $user = User::create(
            [
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'is_admin' => false,
            'request_records' => true,
            'load_records' => true,
            'view_employee_info' => false,
            'sqli_on' => false,
            'file_upload_on' => false,
            'cmd_inject_on' => false,
            'xss_reflected_on' => false,
            'xss_stored_on' => false,
            'idor_on' => false,
            ]
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
