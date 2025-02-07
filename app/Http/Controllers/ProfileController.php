<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Services\ChangePasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected $changePasswordService;

    public function __construct(ChangePasswordService $changePasswordService)
    {
        $this->changePasswordService = $changePasswordService;
    }

    public function index(Request $request): View
    {
        return view('profile.change-password');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $success = $this->changePasswordService->updatePassword($user, $request);

        if ($success) {
            return redirect()->route('profile.change-password')
                             ->with('status', 'password-updated');
        }

        return redirect()->route('profile.change-password')
                         ->withErrors(['current_password' => 'Current password is incorrect.']);
    }
}