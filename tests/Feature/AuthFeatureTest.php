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

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
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

    public function test_registration_fails_with_duplicate_username(): void
    {
        User::factory()->create(['username' => 'testuser']);

        $response = $this->post(
            route('register.attempt'), [
                '_token' => session()->token(),
                'username' => 'testuser',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]
        );

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->post(
            route('register.attempt'), [
                '_token' => session()->token(),
                'username' => 'testuser',
                'password' => '123',
                'password_confirmation' => '123',
            ]
        );

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_fails_without_csrf_token(): void
    {
        $response = $this->post(
            route('register.attempt'), [
                'username' => 'testuser',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]
        );

        $response->assertStatus(419); // CSRF token mismatch
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_access_register_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('register'));
        $response->assertRedirect(route('dashboard'));
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

    public function test_login_fails_with_missing_credentials(): void
    {
        $response = $this->post(
            route('login.attempt'), [
                '_token' => session()->token(),
                'username' => '',
                'password' => '',
            ]
        );

        $response->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_username(): void
    {
        $response = $this->post(
            route('login.attempt'), [
                '_token' => session()->token(),
                'username' => 'nonexistent',
                'password' => 'password123',
            ]
        );

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_login_fails_without_csrf_token(): void
    {
        $response = $this->post(
            route('login.attempt'), [
                'username' => 'testuser',
                'password' => 'password123',
            ]
        );

        $response->assertStatus(419); // CSRF token mismatch
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


    public function test_logout_while_not_logged_in(): void
    {
        $response = $this->post(
            route('logout'), [
                '_token' => session()->token(),
            ]
        );

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_session_is_invalidated_on_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(
            route('logout'), [
                '_token' => session()->token(),
            ]
        );

        $response->assertRedirect('/');
        $this->assertGuest();

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }


    public function test_session_is_regenerated_on_login(): void
    {
        User::factory()->create(
            [
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            ]
        );

        $initialSessionId = session()->getId();

        $response = $this->post(
            route('login.attempt'), [
                '_token' => session()->token(),
                'username' => 'testuser',
                'password' => 'password123',
            ]
        );

        $response->assertRedirect(route('dashboard'));
        $this->assertNotEquals($initialSessionId, session()->getId());
    }

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('login'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_session_persists_across_requests(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // First request
        $response = $this->get(route('dashboard'));
        $response->assertOk();

        // Second request
        $response = $this->get(route('profile.edit'));
        $response->assertOk();

        $this->assertAuthenticatedAs($user);
    }
}
