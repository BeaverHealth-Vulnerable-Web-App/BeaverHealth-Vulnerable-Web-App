<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SQLController extends Controller
{
    public function showMainPage()
    {
        return view('sql.sqlmainpage');
    }

    public function level1()
    {
        return view('sql.sql1');
    }

    // Handle form submission and query
    public function processLevel1(Request $request)
    {
        // Get the firstname input
        $firstname = $request->input('firstname');

        // Run the SQL query (keeping original approach for SQL injection demonstration)
        $result = DB::select("SELECT lastname FROM users WHERE firstname = ?", [$firstname]);

        // Pass the result to the view
        if (count($result) > 0) {
            return view('sql.sql1', ['lastname' => $result[0]->lastname]);
        } else {
            return view('sql.sql1', ['noResults' => '0 results']);
        }
    }
}
