<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => $this->faker->userName,
            'password' => Hash::make($this->faker->password),
            'is_admin' => false,
            'request_records' => $this->faker->boolean(20),
            'load_records' => $this->faker->boolean(20),
            'view_employee_info' => $this->faker->boolean(20),
            'sqli_on' => $this->faker->boolean(20),
            'file_upload_on' => false,
            'cmd_inject_on' => false,
            'xss_reflected_on' => false,
            'xss_stored_on' => false,
            'idor_on' => false,
        ];
    }
}
