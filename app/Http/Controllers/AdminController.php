<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show the admin panel with all users and their roles.
     */
    public function index()
    {
        // Fetch all users
        $users = User::all();

        // Return the admin view with the list of users
        return view('admin', compact('users'));
    }

    /**
     * Update a user's role via AJAX.
     */
    public function updateRole(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if ($user) {
            $role = $request->input('role');
            $value = $request->input('value') ? true : false;

            // Update the role
            $user->$role = $value;
            $user->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }
}
