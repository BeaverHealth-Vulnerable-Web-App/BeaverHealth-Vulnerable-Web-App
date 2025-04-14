<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PatientInfoService
{
    public function searchPatients($searchTerm)
    {
        $user = Auth::user();
        if ($user && $user->sqli_on) {
            $query = "SELECT * FROM patient WHERE first_name LIKE '%$searchTerm%' " .
                     "OR last_name LIKE '%$searchTerm%'";
            return DB::select($query);
        }
        return DB::select(
            "SELECT * FROM patient WHERE first_name LIKE ? OR last_name LIKE ?",
            ["%$searchTerm%", "%$searchTerm%"]
        );
    }
}
