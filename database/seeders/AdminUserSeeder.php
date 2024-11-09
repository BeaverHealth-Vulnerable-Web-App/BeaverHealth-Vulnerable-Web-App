<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        if (!User::where('email', 'admin@admin.com')->exists()) {
            $admin = User::create(
                [
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                ]
            );

            $admin->role()->create(
                [
                'administrator' => true,
                'records_request' => true,
                'records_add' => true,
                ]
            );
        }
    }
}
