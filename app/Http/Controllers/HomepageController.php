<?php

namespace App\Http\Controllers;

class HomepageController extends Controller
{
    public function showHomepage()
    {
        return view('home');
    }
}
