<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Services\UserRoleService;

class AdminController extends Controller
{
    private UserRoleService $roleService;

    public function __construct(UserRoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $users = User::all();
        return view('admin', compact('users'));
    }

    public function updateRole(UpdateUserRoleRequest $request)
    {
        $user = User::findOrFail($request->input('user_id'));
        $success = $this->roleService->updateRole(
            $user,
            $request->input('role'),
            $request->boolean('value')
        );

        if (!$success) {
            return response()->json(
                ['success' => false, 'message' => 'Failed to update role'],
                500
            );
        }

        return response()->json(['success' => true]);
    }
}
