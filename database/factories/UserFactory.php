<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'is_admin' => false,
            'request_records' => true,
            'load_records' => true,
            'view_employee_info' => false,
            'sqli_on' => false,
            'file_upload_on' => false,
            'cmd_inject_on' => false,
            'xss_reflected_on' => false,
            'xss_stored_on' => false,
            'idor_on' => false,
        ];
    }
}
