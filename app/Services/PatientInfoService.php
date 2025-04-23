<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Patient;

class PatientInfoService
{
    /**
     * @param  string  $searchTerm  The term to search for.
     * @param  bool    $sqliOn      If true, run the vulnerable raw SQL; otherwise use Eloquent ORM.
     * @return array|Collection<Patient>
     */
    public function searchPatients(string $searchTerm, bool $sqliOn): array|Collection
    {
        if ($sqliOn) {
            // === VULNERABLE ===
            $sql = <<<SQL
                SELECT *
                FROM patient
                WHERE first_name LIKE '%{$searchTerm}%'
                   OR last_name  LIKE '%{$searchTerm}%'
            SQL;

            return DB::select($sql);
        }

        // === SAFE ===
        return Patient::whereRaw(
            'CONCAT(first_name, last_name) LIKE ?',
            ["%{$searchTerm}%"]
        )->get();
    }
}
