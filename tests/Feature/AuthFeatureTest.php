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
                route('login.attempt'),
                [
                    'username' => self::TEST_USERNAME,
                    'password' => self::TEST_WRONG_PASSWORD,
                ]
            );
        }
    }

    public function testGuestIsRedirectedFromDashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function testUserCanRegister(): void
    {
        $this->get(route('register'));

        $this->postWithCsrf(
            route('register.attempt'),
            [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
                'password_confirmation' => self::TEST_PASSWORD,
            ]
        )->assertRedirect(route('dashboard'));

        $this->assertAuthenticated()
            ->assertDatabaseHas('user', ['username' => 'testuser']);
    }

    public function testRegistrationFailsWithDuplicateUsername(): void
    {
        $this->createTestUser();
        $this->get(route('register'));

        $this->postWithCsrf(
            route('register.attempt'),
            [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
                'password_confirmation' => self::TEST_PASSWORD,
            ]
        )->assertSessionHasErrors(['username']);

        $this->assertGuest();
    }

    public function testRegistrationFailsWithWeakPassword(): void
    {
        $this->get(route('register'));

        $this->postWithCsrf(
            route('register.attempt'),
            [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_WEAK_PASSWORD,
                'password_confirmation' => self::TEST_WEAK_PASSWORD,
            ]
        )->assertSessionHasErrors(['password']);

        $this->assertGuest();
    }

    public function testRegistrationFailsWithoutCsrfToken(): void
    {
        $this->get(route('register'));

        $this->post(
            route('register.attempt'),
            [
                'password' => self::TEST_PASSWORD,
                'password_confirmation' => self::TEST_PASSWORD,
            ]
        )->assertCsrfMismatch();

        $this->assertGuest();
    }

    public function testAuthenticatedUserCannotAccessRegisterPage(): void
    {
        $user = $this->createTestUser();
        $this->actingAs($user);

        $this->get(route('register'))
            ->assertRedirect(route('dashboard'));
    }

    public function testUserCanLoginWithValidCredentials(): void
    {
        $user = $this->createTestUser();
        $this->get(route('login'));

        $this->postWithCsrf(
            route('login.attempt'),
            [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
            ]
        )->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function testUserCannotLoginWithInvalidCredentials(): void
    {
        $this->createTestUser();
        $this->get(route('login'));

        $this->post(
            route('login.attempt'),
            [
                '_token' => session()->token(),
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_WRONG_PASSWORD,
            ]
        )->assertSessionHasErrors(['username']);

        $this->assertGuest();
    }

    public function testLoginFailsWithMissingCredentials(): void
    {
        $this->get(route('login'));

        $this->postWithCsrf(
            route('login.attempt'),
            [
                'username' => '',
                'password' => '',
            ]
        )->assertSessionHasErrors(['username', 'password']);

        $this->assertGuest();
    }

    public function testLoginFailsWithNonexistentUsername(): void
    {
        $this->get(route('login'));

        $this->postWithCsrf(
            route('login.attempt'),
            [
                'username' => self::TEST_NONEXISTENT_USERNAME,
                'password' => self::TEST_PASSWORD,
            ]
        )->assertSessionHasErrors(['username']);

        $this->assertGuest();
    }

    public function testLoginFailsWithoutCsrfToken(): void
    {
        $this->get(route('login'));

        $this->post(
            route('login.attempt'),
            [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
            ]
        )->assertCsrfMismatch();

        $this->assertGuest();
    }

    public function testUserCanLogout(): void
    {
        $user = $this->createTestUser();
        $this->actingAs($user);
        $this->get(route('dashboard'));

        $this->postWithCsrf(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function testLogoutWhileNotLoggedIn(): void
    {
        $this->startSession();

        $this->postWithCsrf(route('logout'))
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function testSessionIsInvalidatedOnLogout(): void
    {
        $user = $this->createTestUser();
        $this->actingAs($user);
        $this->get(route('dashboard'));

        $this->postWithCsrf(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }


    public function testSessionIsRegeneratedOnLogin(): void
    {
        $this->createTestUser();
        $this->get(route('login'));

        $initialSessionId = session()->getId();

        $this->postWithCsrf(
            route('login.attempt'),
            [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_PASSWORD,
            ]
        )->assertRedirect(route('dashboard'));

        $this->assertNotEquals($initialSessionId, session()->getId());
    }

    public function testAuthenticatedUserCannotAccessLoginPage(): void
    {
        $user = $this->createTestUser();
        $this->actingAs($user);

        $this->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function testAuthenticatedSessionPersistsAcrossRequests(): void
    {
        $user = $this->createTestUser();
        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertOk();

        $this->get(route('profile.change-password'))
            ->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function testLoginLockoutAfterFailedAttempts(): void
    {
        $this->createTestUser();

        $this->get(route('login'))
            ->assertOk();

        $this->triggerLoginLockout();

        $response = $this->postWithCsrf(
            route('login.attempt'),
            [
                'username' => self::TEST_USERNAME,
                'password' => self::TEST_WRONG_PASSWORD,
            ]
        )->assertFound();

        $errors = (new TestResponse($response)) // TestResponse needed to access session errors
            ->session()
            ->get('errors')
            ->getBag('default')
            ->get('username');

        $this->assertStringContainsString('Too many login attempts', $errors[0]);
    }
}
