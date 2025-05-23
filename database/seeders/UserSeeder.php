<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Uses APP_ENV to determine whether to create demo or dev users, and
     * uses NUM_FAKE_USERS to determine how many fake users to create.
     *
     * @return void
     */
    public function run(): void
    {
        if (config('app.env') === 'demo') {
            $this->createDemoUsers();
        } else {
            $this->createDevUser();
        }

        User::factory()
            ->count(config('app.num_fake_users', 10))
            ->create();
    }

    /**
     * Create a development user with elevated permissions.
     *
     * @return void
     */
    private function createDevUser(): void
    {
        $this->createUser(
            'dev',
            env('DEV_USER_PASSWORD'),
            [
                'is_admin' => true,
                'view_patient_info' => true
            ]
        );
    }

    /**
     * Create demo users with standard permissions.
     *
     * @return void
     */
    private function createDemoUsers(): void
    {
        $this->createUser('gokul', env('GOKUL_USER_PASSWORD'));
        $this->createUser('grader', env('GRADER_USER_PASSWORD'));
        $this->createUser('cody', env('CODY_USER_PASSWORD'));
        $this->createUser('brynn', env('BRYNN_USER_PASSWORD'));
        $this->createUser('sean', env('SEAN_USER_PASSWORD'));
        $this->createUser('alexa', env('ALEXA_USER_PASSWORD'));
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
