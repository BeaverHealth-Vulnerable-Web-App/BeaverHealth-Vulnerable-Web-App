<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AccessControlFeatureTest extends TestCase
{
    protected function createUserWithRoles($roles = [])
    {
        return User::factory()->create(
            array_merge(
                [
                    'is_admin' => false,
                    'request_records' => false,
                    'load_records' => false,
                    'view_patient_info' => false,
                ],
                $roles
            )
        );
    }

    public function testAdminCanAccessAdminPage()
    {
        $admin = $this->createUserWithRoles(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin'))
            ->assertOk()
            ->assertViewIs('admin');
    }

    public function testAdminCanUpdateRoles()
    {
        $admin = $this->createUserWithRoles(['is_admin' => true]);
        $targetUser = $this->createUserWithRoles();

        $this->get(route('admin'));

        $roles = ['is_admin', 'request_records', 'load_records', 'view_patient_info'];
        foreach ($roles as $role) {
            $this->actingAs($admin)->postWithCsrf(
                route('admin.updateRole'),
                [
                    'user_id' => $targetUser->user_id,
                    'role' => $role,
                    'value' => true
                ]
            )
            ->assertOk()
            ->assertJson(['success' => true]);
        }

        $updatedUser = $targetUser->fresh();
        foreach ($roles as $role) {
            $this->assertTrue($updatedUser->$role);
        }
    }

    // This is expected behavior in the vulnerable version of the admin panel
    public function testNonAdminCanUpdateUserRoles()
    {
        $admin = $this->createUserWithRoles();
        $targetUser = $this->createUserWithRoles();

        $this->get(route('admin'));

        $roles = ['is_admin', 'request_records', 'load_records', 'view_patient_info'];
        foreach ($roles as $role) {
            $this->actingAs($admin)->postWithCsrf(
                route('admin.updateRole'),
                [
                    'user_id' => $targetUser->user_id,
                    'role' => $role,
                    'value' => true
                ]
            )
            ->assertOk()
            ->assertJson(['success' => true]);
        }

        $updatedUser = $targetUser->fresh();
        foreach ($roles as $role) {
            $this->assertTrue($updatedUser->$role);
        }
    }

    public function testNavigationLinksVisibleWithRoles()
    {
        $user = $this->createUserWithRoles(
            [
                'is_admin' => true,
                'request_records' => true,
                'load_records' => true,
                'view_patient_info' => true,
            ]
        );

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('href="' . route('records.request') . '"', false)
            ->assertSee('href="' . route('records.add') . '"', false)
            ->assertSee('href="' . route('admin') . '"', false)
            ->assertSee('href="' . route('patients.index') . '"', false);
    }

    public function testNavigationLinksHiddenWithoutRoles()
    {
        $user = $this->createUserWithRoles();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('href="' . route('records.request') . '"', false)
            ->assertDontSee('href="' . route('records.add') . '"', false)
            ->assertDontSee('href="' . route('admin') . '"', false)
            ->assertDontSee('href="' . route('patients.index') . '"', false);
    }

    public function testInvalidRoleRejected()
    {
        $admin = $this->createUserWithRoles(['is_admin' => true]);
        $targetUser = $this->createUserWithRoles();

        $this->get(route('admin'));
        $this->actingAs($admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->postWithCsrf(
                route('admin.updateRole'),
                [
                    'user_id' => $targetUser->user_id,
                    'role' => 'invalid_role',
                    'value' => true
                ]
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['role']);
    }


    public function testInvalidUserIdRejected()
    {
        $admin = $this->createUserWithRoles(['is_admin' => true]);

        $this->get(route('admin'));
        $this->actingAs($admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->postWithCsrf(
                route('admin.updateRole'),
                [
                    'user_id' => 9999,
                    'role' => 'request_records',
                    'value' => true
                ]
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);
    }

    public function testInvalidValueTypeRejected()
    {
        $admin = $this->createUserWithRoles(['is_admin' => true]);
        $targetUser = $this->createUserWithRoles();

        $this->get(route('admin'));
        $this->actingAs($admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->postWithCsrf(
                route('admin.updateRole'),
                [
                    'user_id' => $targetUser->user_id,
                    'role' => 'request_records',
                    'value' => 'not-a-bool'
                ]
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['value']);
    }

    public function testMissingFieldsRejected()
    {
        $admin = $this->createUserWithRoles(['is_admin' => true]);

        $this->get(route('admin'));
        $this->actingAs($admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->postWithCsrf(route('admin.updateRole'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['value']);
    }
}
