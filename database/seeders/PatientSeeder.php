<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    public function run()
    {
        DB::table('patient')->insert([
            [
                'first_name'    => 'Ronald',
                'last_name'     => 'Smith',
                'date_of_birth' => '1992-10-21',
                'policy_number' => 'POL-92377',
                'address'       => '45 Applewood Lane, Redwood City, CA 94063',
                'is_employee'   => false,
                'ssn'           => '992-01-5472',
            ],
            [
                'first_name'    => 'Miranda',
                'last_name'     => 'Rodriguez',
                'date_of_birth' => '1985-06-12',
                'policy_number' => 'POL-67142',
                'address'       => '123 Maple Ct, Springfield, IL 62702',
                'is_employee'   => true,
                'ssn'           => '453-28-8888',
            ],
            [
                'first_name'    => 'Alex',
                'last_name'     => 'Liu',
                'date_of_birth' => '1978-03-01',
                'policy_number' => 'POL-12984',
                'address'       => '78 Pine St, Carson City, NV 89701',
                'is_employee'   => false,
                'ssn'           => '678-90-1234',
            ],
            [
                'first_name'    => 'Priya',
                'last_name'     => 'Patel',
                'date_of_birth' => '1995-08-09',
                'policy_number' => 'POL-95452',
                'address'       => '19 Oakwood Rd, Plano, TX 75074',
                'is_employee'   => true,
                'ssn'           => '554-88-2209',
            ],
            [
                'first_name'    => 'Charles',
                'last_name'     => 'Jefferson',
                'date_of_birth' => '1965-12-05',
                'policy_number' => 'POL-56231',
                'address'       => '892 Forest Ave, Boise, ID 83702',
                'is_employee'   => false,
                'ssn'           => '389-74-1066',
            ],
        ]);
    }
}
