<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\TestResponse;

class AuthFeatureTest extends TestCase
{
    private const TEST_USERNAME = 'testuser';
    private const TEST_NONEXISTENT_USERNAME = 'nonexistent';
    private const TEST_PASSWORD = 'password123';
    private const TEST_WEAK_PASSWORD = '123';
    private const TEST_WRONG_PASSWORD = 'wrongpassword';
    private const MAX_LOGIN_ATTEMPTS = 5;

    private function createTestUser(): User
    {
        return User::factory()->create(
            [
                'username' => self::TEST_USERNAME,
                'password' => bcrypt(self::TEST_PASSWORD),
            ]
        );
    }

    private function triggerLoginLockout(): void
    {
        for ($i = 0; $i < self::MAX_LOGIN_ATTEMPTS; $i++) {
            $this->postWithCsrf(
                route('login.attempt'), [
                    'username' => self::TEST_USERNAME,
                    'password' => self::TEST_WRONG_PASSWORD,
                ]
            );
        }
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_register(): void
    {
        $this->get(route('register'));

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
        $this->createTestUser();
        $this->get(route('register'));

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
        $this->get(route('register'));

        $response = $this->postWithCsrf(
            route('register.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_WEAK_PASSWORD,
                'password_confirmation' => self::TEST_WEAK_PASSWORD,
            ]
        );

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_fails_without_csrf_token(): void
    {
        $this->get(route('register'));

        $response = $this->post(
            route('register.attempt'), [
                'password' => self::TEST_PASSWORD,
                'password_confirmation' => self::TEST_PASSWORD,
            ]
        );

        $response->assertCsrfMismatch();
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
        $user = $this->createTestUser();
        $this->get(route('login'));

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
        $this->createTestUser();
        $this->get(route('login'));

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
        $this->get(route('login'));

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
        $this->get(route('login'));

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
        $this->get(route('login'));

        $response = $this->post(
            route('login.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
            ]
        );

        $response->assertCsrfMismatch();
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get(route('dashboard'));

        $response = $this->postWithCsrf(route('logout'));
        $response->assertRedirect('/');
        $this->assertGuest();
    }


    public function test_logout_while_not_logged_in(): void
    {
        $this->startSession();
        $response = $this->postWithCsrf(route('logout'));
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_session_is_invalidated_on_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get(route('dashboard'));

        $response = $this->postWithCsrf(route('logout'));
        $response->assertRedirect('/');
        $this->assertGuest();

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }


    public function test_session_is_regenerated_on_login(): void
    {
        $this->createTestUser();
        $this->get(route('login'));

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

    public function test_login_lockout_after_failed_attempts(): void
    {
        $this->createTestUser();
        $this->get(route('login'))->assertOk();

        $this->triggerLoginLockout();

        $response = $this->postWithCsrf(
            route('login.attempt'), [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_WRONG_PASSWORD,
            ]
        );

        $response->assertFound();

        $errors = (new TestResponse($response))
            ->session()
            ->get('errors')
            ->getBag('default')
            ->get('username');

        $this->assertStringContainsString('Too many login attempts', $errors[0]);
    }
}
