<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Services\UserRoleService;

class AdminController extends Controller
{
    /**
     * Create a new AdminController instance.
     *
     * @param UserRoleService $roleService The role service for handling permission operations
     */
    public function __construct(private UserRoleService $roleService)
    {
    }

    /**
     * Display the admin panel with all users.
     *
     * @return View The admin view with users data
     */
    public function index(): View
    {
        $users = User::all();
        return view('admin', compact('users'));
    }

    /**
     * Update a user's role via AJAX request.
     *
     * @param UpdateUserRoleRequest $request The validated request
     * @return JsonResponse JSON response with success status
     */
    public function updateRole(UpdateUserRoleRequest $request): JsonResponse
    {
        if (!$this->roleService->authorize()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Insufficient permissions'
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        $updateResult = $this->roleService->updateRole(
            User::findOrFail($request->input('user_id')),
            $request->input('role'),
            $request->boolean('value')
        );

        if (!$updateResult['success']) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $updateResult['error'] ?? 'Failed to update role'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(['success' => true]);
    }
}
