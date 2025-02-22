<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BadController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'bad formatting']);
    }
}
