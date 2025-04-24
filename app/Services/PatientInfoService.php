<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class PatientInfoService
 *
 * Handles patient lookups with optional SQL-injection toggle.
 */
class PatientInfoService
{
    /**
     * @param  string               $searchTerm  The term to search for.
     * @param  bool                 $sqliOn      If true, run the vulnerable raw SQL; otherwise use Eloquent ORM.
     * @return array|Collection<Patient>
     */
    public function searchPatients(string $searchTerm, bool $sqliOn): array|Collection
    {
        if ($sqliOn) {
            // vulnerable
            $sql = <<<SQL
                SELECT *
                FROM patient
                WHERE CONCAT(first_name, last_name) LIKE '%{$searchTerm}%'
            SQL;
            return DB::select($sql);
        }

        // safe
        return Patient::whereRaw(
            'CONCAT(first_name, last_name) LIKE ?',
            ["%{$searchTerm}%"]
        )->get();
    }
}
