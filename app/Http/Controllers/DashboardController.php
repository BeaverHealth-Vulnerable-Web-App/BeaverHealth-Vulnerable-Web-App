<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
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
