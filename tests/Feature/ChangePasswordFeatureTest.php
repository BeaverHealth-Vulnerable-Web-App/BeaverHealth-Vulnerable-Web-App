<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;

class ChangePasswordFeatureTest extends TestCase
{
    private User $user;

    protected function createTestUser(string $password = 'password123'): User
    {
        return User::factory()->create([
            'password' => bcrypt($password),
        ]);
    }

    private function submitPasswordChange(array $data, bool $withReferer = false): TestResponse
    {
        $this->actingAs($this->user);

        $this->get(route('profile.change-password'));

        $request = $this->withHeaders(
            $withReferer
                ? ['referer' => route('profile.change-password')]
                : []
        );

        return $request->postWithCsrf(
            route('profile.change-password.update'),
            $data
        );
    }

    public function testChangePasswordPageAccessible(): void
    {
        $this->user = $this->createTestUser();

        $this->actingAs($this->user)
             ->get(route('profile.change-password'))
             ->assertOk()
             ->assertViewIs('profile.change-password');
    }

    public function testValidPasswordChange(): void
    {
        $this->user = $this->createTestUser('oldpassword');

        $response = $this->submitPasswordChange([
            'username_confirmation' => $this->user->username,
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHas('change-password-status');

        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    public function testChangePasswordFailsWithIncorrectCurrentPassword(): void
    {
        $this->user = $this->createTestUser('oldpassword');

        $response = $this->submitPasswordChange([
            'username_confirmation' => $this->user->username,
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ], true);

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['current_password']);

        $this->assertTrue(Hash::check('oldpassword', $this->user->fresh()->password));
    }

    public function testSqlInjectionAttemptFails(): void
    {
        $this->user = $this->createTestUser('oldpassword');

        $response = $this->submitPasswordChange([
            'username_confirmation' => "' OR '1'='1",
            'current_password' => 'whatever',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ], true);

        $response->assertRedirect(route('profile.change-password'));
    }

    public function testSamePasswordChange(): void
    {
        $this->user = $this->createTestUser('oldpassword');

        $response = $this->submitPasswordChange([
            'username_confirmation' => $this->user->username,
            'current_password' => 'oldpassword',
            'password' => 'oldpassword',
            'password_confirmation' => 'oldpassword',
        ], true);

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHas('change-password-status');

        $this->assertTrue(Hash::check('oldpassword', $this->user->fresh()->password));
    }

    public function testEdgeCaseInputForPasswordChange(): void
    {
        $this->user = $this->createTestUser('oldpassword');
        $longPassword = str_repeat('A!a1', 50);

        $response = $this->submitPasswordChange([
            'username_confirmation' => $this->user->username,
            'current_password' => 'oldpassword',
            'password' => $longPassword,
            'password_confirmation' => $longPassword,
        ], true);

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHas('change-password-status');

        $this->assertTrue(Hash::check($longPassword, $this->user->fresh()->password));
    }

    public function testMissingFields(): void
    {
        $this->user = $this->createTestUser('oldpassword');

        $response = $this->submitPasswordChange([], true);

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['username_confirmation', 'current_password', 'password']);
    }

    public function testPasswordConfirmationMismatch(): void
    {
        $this->user = $this->createTestUser('oldpassword');

        $response = $this->submitPasswordChange([
            'username_confirmation' => $this->user->username,
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'wrongconfirmation',
        ], true);

        $response->assertRedirect(route('profile.change-password'))
                 ->assertSessionHasErrors(['password']);
    }

    public function testUnauthenticatedUserCannotAccessChangePassword(): void
    {
        auth()->logout();

        $this->get(route('profile.change-password'))
             ->assertRedirect(route('login'));

        $this->post(route('profile.change-password.update'), [])
             ->assertStatus(419); // No CSRF token
    }
}
