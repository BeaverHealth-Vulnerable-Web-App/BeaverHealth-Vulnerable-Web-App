<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Real users
        $this->createUser('gokul', 'eN%f0G6M!xGiX@^K7$');
        $this->createUser('cody', 'cBLc8*R1EGu56t%KD9');
        $this->createUser('brynn', 'Jw%yr95FgYdKXx%Dt6');
        $this->createUser('sean', 'GkNA*yoBrQvTZf4rZP');
        $this->createUser('alexa', 'Mo8*AqTe6vaUm0f98j');

        // Fake users
        User::factory()->count(10)->create();
    }

    private function createUser(string $username, string $password): void
    {
        User::updateOrCreate(
            [
                'username' => $username,
                'password' => bcrypt($password),
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
    }
}
