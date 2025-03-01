<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AccessControlFeatureTest extends TestCase
{
    protected function createUser($roles = [], $idor_on = false)
    {
        return User::factory()->create(
            array_merge(
                [
                    'is_admin' => false,
                    'request_records' => false,
                    'load_records' => false,
                    'view_patient_info' => false,
                    'idor_on' => $idor_on,
                ],
                $roles
            )
        );
    }

    public static function accessControlProvider(): array
    {
        return [
            'admin page, no roles, idor off' => [
                'route' => 'admin',
                'roles' => [],
                'idor_on' => false,
                'should_have_access' => false,
                'expected_view' => null
            ],
            'admin page, admin role, idor off' => [
                'route' => 'admin',
                'roles' => ['is_admin' => true],
                'idor_on' => false,
                'should_have_access' => true,
                'expected_view' => 'admin'
            ],
            'admin page, no roles, idor on' => [
                'route' => 'admin',
                'roles' => [],
                'idor_on' => true,
                'should_have_access' => true,
                'expected_view' => 'admin'
            ],
            'records.request, no roles, idor off' => [
                'route' => 'records.request',
                'roles' => [],
                'idor_on' => false,
                'should_have_access' => false,
                'expected_view' => null
            ],
            'records.request, request_records role, idor off' => [
                'route' => 'records.request',
                'roles' => ['request_records' => true],
                'idor_on' => false,
                'should_have_access' => true,
                'expected_view' => 'records.request'
            ],
            'records.request, no roles, idor on' => [
                'route' => 'records.request',
                'roles' => [],
                'idor_on' => true,
                'should_have_access' => true,
                'expected_view' => 'records.request'
            ],
            'records.add, no roles, idor off' => [
                'route' => 'records.add',
                'roles' => [],
                'idor_on' => false,
                'should_have_access' => false,
                'expected_view' => null
            ],
            'records.add, load_records role, idor off' => [
                'route' => 'records.add',
                'roles' => ['load_records' => true],
                'idor_on' => false,
                'should_have_access' => true,
                'expected_view' => 'records.add'
            ],
            'records.add, no roles, idor on' => [
                'route' => 'records.add',
                'roles' => [],
                'idor_on' => true,
                'should_have_access' => true,
                'expected_view' => 'records.add'
            ],
            'patients.index, no roles, idor off' => [
                'route' => 'patients.index',
                'roles' => [],
                'idor_on' => false,
                'should_have_access' => false,
                'expected_view' => null
            ],
            'patients.index, view_patient_info role, idor off' => [
                'route' => 'patients.index',
                'roles' => ['view_patient_info' => true],
                'idor_on' => false,
                'should_have_access' => true,
                'expected_view' => 'patients.index'
            ],
            'patients.index, no roles, idor on' => [
                'route' => 'patients.index',
                'roles' => [],
                'idor_on' => true,
                'should_have_access' => true,
                'expected_view' => 'patients.index'
            ],
        ];
    }

    #[DataProvider('accessControlProvider')]
    public function testAccessControl($route, $roles, $idor_on, $should_have_access, $expected_view)
    {
        $response = $this->actingAs(
            $this->createUser($roles, $idor_on)
        )->get(route($route));

        if ($should_have_access) {
            $response->assertOk();
            if ($expected_view) {
                $response->assertViewIs($expected_view);
            }
        } else {
            // Using assertStringStartsWith and assertMatchesRegularExpression instead of assertRedirect because
            // our implementation adds a timestamp parameter to the dashboard URL for cache-busting on back button
            $redirectUrl = $response->headers->get('Location');
            $this->assertStringStartsWith(
                rtrim(route('dashboard'), '/'),
                $redirectUrl,
                "Response does not redirect to the dashboard route"
            );
            $this->assertMatchesRegularExpression(
                '/\/\d+$/',
                $redirectUrl,
                'Redurect URL does not end with a numeric timestamp parameter'
            );
            $response->assertFound()
                    ->assertSessionHas('access-status', [
                        'type' => 'error',
                        'message' => 'Access denied: You do not have permission to view this page.'
                    ]);
        }
    }

    public static function roleToggleProvider(): array
    {
        return [
            'admin can toggle roles with idor on' => [
                'roles' => ['is_admin' => true],
                'idor_on' => true,
                'should_succeed' => true
            ],
            'admin can toggle roles with idor off' => [
                'roles' => ['is_admin' => true],
                'idor_on' => false,
                'should_succeed' => true
            ],
            'non-admin can toggle roles with idor on' => [
                'roles' => [],
                'idor_on' => true,
                'should_succeed' => true
            ],
            'non-admin cannot toggle roles with idor off' => [
                'roles' => [],
                'idor_on' => false,
                'should_succeed' => false
            ]
        ];
    }

    #[DataProvider('roleToggleProvider')]
    public function testRoleToggling(array $roles, bool $idor_on, bool $should_succeed)
    {
        $user = $this->createUser($roles, $idor_on);
        $targetUser = $this->createUser();

        $this->get(route('admin'));
        $roleNames = ['is_admin', 'request_records', 'load_records', 'view_patient_info'];

        // Toggle roles on
        foreach ($roleNames as $role) {
            $response = $this->actingAs($user)->postWithCsrf(
                route('admin.updateRole'),
                [
                'user_id' => $targetUser->user_id,
                'role' => $role,
                'value' => true
                ]
            );

            if ($should_succeed) {
                $response->assertOk()
                    ->assertJson(['success' => true]);
            } else {
                $response->assertForbidden()
                    ->assertJson([
                        'success' => false,
                        'message' => 'Insufficient permissions'
                    ]);
            }
        }

        $updatedUser = $targetUser->fresh();
        foreach ($roleNames as $role) {
            if ($should_succeed) {
                $this->assertTrue($updatedUser->$role);
            } else {
                $this->assertFalse($updatedUser->$role);
            }
        }

        // Toggle roles off
        foreach ($roleNames as $role) {
            $response = $this->actingAs($user)->postWithCsrf(
                route('admin.updateRole'),
                [
                'user_id' => $targetUser->user_id,
                'role' => $role,
                'value' => false
                ]
            );

            if ($should_succeed) {
                $response->assertOk()
                    ->assertJson(['success' => true]);
            } else {
                $response->assertForbidden()
                    ->assertJson([
                        'success' => false,
                        'message' => 'Insufficient permissions'
                    ]);
            }
        }

        $updatedUser = $targetUser->fresh();
        foreach ($roleNames as $role) {
            $this->assertFalse($updatedUser->$role);
        }
    }

    public static function updateRoleDataProvider(): array
    {
        return [
            'invalid_role' => [
                ['role' => 'invalid_role', 'value' => true],
                ['role']
            ],
            'array_of_roles' => [
                ['role' => ['is_admin', 'request_records', 'load_records', 'view_patient_info'], 'value' => true],
                ['role']
            ],
            'invalid_user_id' => [
                ['user_id' => 9999, 'role' => 'request_records', 'value' => true],
                ['user_id']
            ],
            'invalid_value_type' => [
                ['role' => 'request_records', 'value' => 'not-a-bool'],
                ['value']
            ],
            'invalid_role_type' => [
                ['role' => 2, 'value' => true],
                ['role']
            ],
        ];
    }

    #[DataProvider('updateRoleDataProvider')]
    public function testUpdateRoleValidation(array $requestData, array $expectedErrors)
    {
        if (!isset($requestData['user_id'])) {
            $requestData['user_id'] = $this->createUser()->user_id;
        }

        if (!empty($requestData)) {
            $baseData = [
                'user_id' => $requestData['user_id'],
                'role' => 'request_records',
                'value' => true
            ];
            $requestData = array_merge($baseData, $requestData);
        }

        $this->get(route('admin'));
        $this->actingAs($this->createUser(['is_admin' => true]))
            ->withHeaders(['Accept' => 'application/json'])
            ->postWithCsrf(
                route('admin.updateRole'),
                $requestData
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors($expectedErrors);
    }

    public function testRepeatedRoleUpdatesWork()
    {
        $targetUser = $this->createUser();
        $this->get(route('admin'));
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($this->createUser(['is_admin' => true]))
                ->withHeaders(['Accept' => 'application/json'])
                ->postWithCsrf(
                    route('admin.updateRole'),
                    [
                        'user_id' => $targetUser->user_id,
                        'role' => 'request_records',
                        'value' => true
                    ]
                )
                ->assertOk();
        }
        $this->assertTrue($targetUser->fresh()->request_records);
    }

    public function testNavigationLinksVisibleWithRoles()
    {
        $this->actingAs(
            $this->createUser(
                [
                    'is_admin' => true,
                    'request_records' => true,
                    'load_records' => true,
                    'view_patient_info' => true,
                ]
            )
        )
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('href="' . route('records.request') . '"', false)
        ->assertSee('href="' . route('records.add') . '"', false)
        ->assertSee('href="' . route('admin') . '"', false)
        ->assertSee('href="' . route('patients.index') . '"', false);
    }

    public function testNavigationLinksHiddenWithoutRoles()
    {
        $this->actingAs($this->createUser())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('href="' . route('records.request') . '"', false)
            ->assertDontSee('href="' . route('records.add') . '"', false)
            ->assertDontSee('href="' . route('admin') . '"', false)
            ->assertDontSee('href="' . route('patients.index') . '"', false);
    }

    public function testProtectedPagesHaveCacheControlHeaders()
    {
        $response = $this->actingAs($this->createUser(['load_records' => true]))
                        ->get(route('records.add'))
                        ->assertHeader('Pragma', 'no-cache');

        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);

        $this->assertLessThan(
            time(),
            strtotime($response->headers->get('Expires')),
            'Expires header should be in the past'
        );
    }
}
