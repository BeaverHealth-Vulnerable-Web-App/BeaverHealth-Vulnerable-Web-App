<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->startSession();
    }

    public function test_user_can_register(): void
    {
        $response = $this->post(
            route('register.attempt'), [
                '_token' => session()->token(),
                'username' => 'testuser',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]
        );

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('user', ['username' => 'testuser']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(
            [
                'username' => 'testuser',
                'password' => bcrypt('password123'),
            ]
        );

        $response = $this->post(
            route('login.attempt'), [
                '_token' => session()->token(),
                'username' => 'testuser',
                'password' => 'password123',
            ]
        );

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create(
            [
                'username' => 'testuser',
                'password' => bcrypt('password123'),
            ]
        );

        $response = $this->post(
            route('login.attempt'), [
                '_token' => session()->token(),
                'username' => 'testuser',
                'password' => 'wrongpassword',
            ]
        );

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(
            route('logout'), [
                '_token' => session()->token()
            ]
        );

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
