<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('patient')->insert([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date_of_birth' => '1980-01-01',
                'policy_number' => 'POLICY1001',
                'address' => '123 Main St, Anytown, USA',
                'is_employee' => false
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'date_of_birth' => '1990-02-01',
                'policy_number' => 'POLICY1002',
                'address' => '456 Elm St, Anycity, USA',
                'is_employee' => true
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'date_of_birth' => '1975-05-15',
                'policy_number' => 'POLICY1003',
                'address' => '789 Maple St, Anystate, USA',
                'is_employee' => false
            ]
        ]);
    }
}
