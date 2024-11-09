<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'User 1',
                'email' => 'user1@example.com',
                'password' => bcrypt('password'),
                'role' => [
                    'administrator' => false,
                    'records_request' => true,
                    'records_add' => false,
                ]
            ],
            [
                'name' => 'User 2',
                'email' => 'user2@example.com',
                'password' => bcrypt('password'),
                'role' => [
                    'administrator' => false,
                    'records_request' => true,
                    'records_add' => true,
                ]
            ],
        ];

        foreach ($users as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                $user = User::create(
                    [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    ]
                );

                $user->role()->create($userData['role']);
            }

        }
    }
}
