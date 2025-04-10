<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Uses APP_ENV to determine which users to create.
     *
     * @return void
     */
    public function run(): void
    {
        match (config('app.env')) {
            'local' => $this->createDevUser(),
            'prod'  => $this->createProdUsers(),
            default => null
        };
    }

    /**
     * Create a development user with elevated permissions.
     *
     * @return void
     */
    private function createDevUser(): void
    {
        $this->createUser('dev', 'password', [
            'is_admin' => true,
            'view_patient_info' => true
        ]);
    }

    /**
     * Create production users with standard permissions.
     *
     * @return void
     */
    private function createProdUsers(): void
    {
        $this->createUser('gokul', 'eN%f0G6M!xGiX@^K7$');
        $this->createUser('grader', 'JRhAT%YzGx1iSn!U6i');
        $this->createUser('cody', 'cBLc8*R1EGu56t%KD9');
        $this->createUser('brynn', 'Jw%yr95FgYdKXx%Dt6');
        $this->createUser('sean', 'GkNA*yoBrQvTZf4rZP');
        $this->createUser('alexa', 'Mo8*AqTe6vaUm0f98j');
    }

    /**
     * Create a new user.
     *
     * @param string $username The username for the new user
     * @param string $password The plaintext password for the new user
     * @param array $overrides Optional key-value pairs to override default user roles
     *
     * @return void
     */
    private function createUser(string $username, string $password, array $overrides = []): void
    {
        $defaults = [
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
        ];

        User::firstOrCreate(
            ['username' => $username],
            array_merge($defaults, $overrides)
        );
    }
}
