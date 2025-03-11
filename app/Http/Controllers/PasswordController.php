<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Services\ChangePasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class PasswordController extends Controller
{
    protected $changePasswordService;

    public function __construct(ChangePasswordService $changePasswordService)
    {
        $this->changePasswordService = $changePasswordService;
    }

    public function index(): View
    {
        return view('profile.change-password');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();
        try {
            $result = $this->changePasswordService->updatePassword($user, $request);

            if ($result['success']) {
                session()->flash('change-password-status', [
                    'type' => 'success',
                    'message' => 'Password updated',
                ]);
                return redirect()->route('profile.change-password');
            }

            if ($result['error'] === 'username') {
                return redirect()->route('profile.change-password')
                    ->withErrors(['username_confirmation' => 'Username is incorrect']);
            }

            return redirect()->route('profile.change-password')
                ->withErrors(['current_password' => 'Current password is incorrect']);
        } catch (QueryException $e) {
            if ($user->sqli_on) {
                // Show SQL errors to help troubleshoot the injection payload
                return redirect()->route('profile.change-password')
                    ->withErrors(['username_confirmation' => $e->getMessage()]);
            }
            return redirect()->route('profile.change-password')
                ->withErrors(['current_password' => 'Current password is incorrect']);
        }
    }
}
