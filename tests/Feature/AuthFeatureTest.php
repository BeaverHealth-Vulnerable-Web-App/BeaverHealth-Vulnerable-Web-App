<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_USERNAME = 'testuser';
    private const TEST_NONEXISTENT_USERNAME = 'nonexistent';
    private const TEST_PASSWORD = 'password123';
    private const TEST_BAD_PASSWORD = '123';
    private const TEST_WRONG_PASSWORD = 'wrongpassword';
    private const CSRF_TOKEN_MISMATCH = 419;

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
        $response = $this->postWithCsrf(
            route('register.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
                'password_confirmation' => self::TEST_PASSWORD,
            ]
        );

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('user', ['username' => 'testuser']);
    }

    public function test_registration_fails_with_duplicate_username(): void
    {
        User::factory()->create(['username' => 'testuser']);

        $response = $this->postWithCsrf(
            route('register.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
                'password_confirmation' => self::TEST_PASSWORD,
            ]
        );

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->postWithCsrf(
            route('register.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_BAD_PASSWORD,
                'password_confirmation' => self::TEST_BAD_PASSWORD,
            ]
        );

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_fails_without_csrf_token(): void
    {
        $response = $this->post(
            route('register.attempt'), [
                'password' => self::TEST_PASSWORD,
                'password_confirmation' => self::TEST_PASSWORD,
            ]
        );

        $response->assertStatus(self::CSRF_TOKEN_MISMATCH);
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
                'username' => self::TEST_USERNAME,
                'password' => bcrypt(self::TEST_PASSWORD),
            ]
        );

        $response = $this->postWithCsrf(
            route('login.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
            ]
        );

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create(
            [
                'username' => self::TEST_USERNAME,
                'password' => bcrypt(self::TEST_PASSWORD),
            ]
        );

        $response = $this->post(
            route('login.attempt'), [
                '_token' => session()->token(),
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_WRONG_PASSWORD,
            ]
        );

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_login_fails_with_missing_credentials(): void
    {
        $response = $this->postWithCsrf(
            route('login.attempt'), [
                'username' => '',
                'password' => '',
            ]
        );

        $response->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_username(): void
    {
        $response = $this->postWithCsrf(
            route('login.attempt'), [
                'username' => self::TEST_NONEXISTENT_USERNAME,
                'password' => self::TEST_PASSWORD,
            ]
        );

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_login_fails_without_csrf_token(): void
    {
        $response = $this->post(
            route('login.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
            ]
        );

        $response->assertStatus(self::CSRF_TOKEN_MISMATCH);
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
        $response = $this->postWithCsrf(route('logout'));
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_session_is_invalidated_on_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postWithCsrf(route('logout'), []);
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

        $response = $this->postWithCsrf(
            route('login.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
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
