<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return view('admin', compact('users'));
    }

    public function updateRole(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user && $user->role) {
            $user->role()->update([$request->role => $request->value]);
        }
        return response()->json(['success' => true]);
    }
}
