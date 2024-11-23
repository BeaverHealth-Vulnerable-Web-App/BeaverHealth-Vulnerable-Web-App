<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create the admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => bcrypt('password'),
                'is_admin' => true,
                'request_records' => true,
                'load_records' => true,
                'view_employee_info' => true,
                'sqli_on' => false,
                'file_upload_on' => false,
                'cmd_inject_on' => false,
                'xss_reflected_on' => false,
                'xss_stored_on' => false,
                'idor_on' => false,
            ]
        );

        // Create 10 fake users
        User::factory()->count(10)->create();
    }
}
