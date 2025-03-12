<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Gokul's account
        User::updateOrCreate(
            ['username' => 'gokul'],
            [
                'password' => bcrypt('eN%f0G6M!xGiX@^K7$'),
                'is_admin' => false,
                'request_records' => true,
                'load_records' => true,
                'view_patient_info' => false,
                'sqli_on' => false,
                'file_upload_on' => false,
                'cmd_inject_on' => false,
                'xss_reflected_on' => false,
                'xss_stored_on' => false,
                'bac_on' => false,
            ]
        );

        // Create 10 fake regular users
        User::factory()->count(10)->create();
    }
}
