<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Sessions are initialized via a GET in each test below
    }

    protected function createTestUser(string $password = 'password123'): User
    {
        return User::factory()->create([
            'password' => bcrypt($password),
        ]);
    }

    public function testChangePasswordPageAccessible(): void
    {
        $user = $this->createTestUser();

        $this->actingAs($user)
             ->get(route('profile.change-password'))
             ->assertOk()
             ->assertViewIs('profile.change-password');
    }

    public function testValidPasswordChange(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'current_password'      => 'oldpassword',
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]
        );

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function testChangePasswordFailsWithIncorrectCurrentPassword(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'current_password'      => 'wrongpassword',
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]
        );

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['current_password']);

        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function testSqlInjectionAttemptFails(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'current_password'      => "' OR '1'='1",
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]
        );

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['current_password']);

        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function testCsrfProtection(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        $response = $this->post(
            route('profile.change-password.update'),
            [
                'current_password'      => 'oldpassword',
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]
        );

        $response->assertCsrfMismatch();
        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function testPasswordConfirmationMismatch(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'current_password'      => 'oldpassword',
                'password'              => 'newpassword123',
                'password_confirmation' => 'differentpassword',
            ]
        );

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['password']);

        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function testNewPasswordTooShort(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'current_password'      => 'oldpassword',
                'password'              => 'short',
                'password_confirmation' => 'short',
            ]
        );

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['password']);

        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function testMissingFieldsForPasswordChange(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        // Missing current_password
        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]
        );
        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['current_password']);

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        // Missing password
        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'current_password' => 'oldpassword',
            ]
        );
        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['password']);
    }

    public function testSamePasswordChange(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'current_password'      => 'oldpassword',
                'password'              => 'oldpassword',
                'password_confirmation' => 'oldpassword',
            ]
        );

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function testEdgeCaseInputForPasswordChange(): void
    {
        $user = $this->createTestUser('oldpassword');

        $this->actingAs($user)
             ->get(route('profile.change-password'));

        $longPassword = str_repeat('A!a1', 50); // 200 characters long
        $response = $this->postWithCsrf(
            route('profile.change-password.update'),
            [
                'current_password'      => 'oldpassword',
                'password'              => $longPassword,
                'password_confirmation' => $longPassword,
            ]
        );

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHas('status', 'password-updated');
        $this->assertTrue(Hash::check($longPassword, $user->fresh()->password));
    }
}
