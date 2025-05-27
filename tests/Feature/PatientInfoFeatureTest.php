<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use Tests\TestCase;

class PatientInfoFeatureTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUserWithPermission();
    }

    private function actingAsUserWithPermission(bool $hasPermission = true): void
    {
        $this->user = User::factory()->create([
            'view_patient_info' => $hasPermission
        ]);
        $this->actingAs($this->user);
    }

    public function testPageLoads(): void
    {
        $response = $this->get(route('patients.index'));
        $response->assertOk();
        $response->assertSee('Patient Information');
    }

    public function testSearchReturnsPatients(): void
    {
        $patient = Patient::factory()->create([
            'first_name' => 'Al',
            'last_name' => 'Smith',
        ]);

        $this->get(route('patients.index', ['search' => 'Al']))
            ->assertOk()
            ->assertSeeText('Al Smith')
            ->assertSeeText('Policy Number');
    }

    public function testSearchHandlesInvalidInput(): void
    {
        $this->user->update(['sqli_on' => true]);

        $response = $this->get(route('patients.index', ['search' => "' OR 1=1; --"]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('search');
    }

    public function testSearchWithSqliToggle(): void
    {
        $this->user->update(['sqli_on' => true]);

        $this->get(route('patients.index', ['search' => 'a']))
            ->assertOk()
            ->assertSeeText('Patient');
    }

    public function testUnauthenticatedRedirect(): void
    {
        auth()->logout();

        $this->get(route('patients.index'))
            ->assertRedirect(route('login'));
    }

    public function testAccessDeniedWithoutPermission(): void
    {
        $this->actingAsUserWithPermission(false);

        $response = $this->get(route('patients.index'));

        $response->assertFound();
        $response->assertSessionHas('access-status', [
            'type' => 'error',
            'message' => 'Access denied: You do not have permission to view this page.'
        ]);
    }

    public function testEmptySearchShowsNoResults(): void
    {
        $this->get(route('patients.index', ['search' => '']))
            ->assertOk()
            ->assertDontSeeText('Policy Number');
    }

    public function testSearchWithNoResults(): void
    {
        $this->get(route('patients.index', ['search' => 'NoSuchName']))
            ->assertOk()
            ->assertSeeText('No patients found');
    }

    public function testPatientSearchShowsDetailsLink(): void
    {
        $patient = Patient::factory()->create([
            'first_name' => 'Alex',
            'last_name' => 'Test',
        ]);

        $this->get(route('patients.index', ['search' => 'Alex']))
            ->assertOk()
            ->assertSeeText('View Details')
            ->assertSee(route('patients.info', ['id' => $patient->patient_id]));
    }

    public function testSearchHandlesIntegerInput(): void
    {
        $this->get(route('patients.index', ['search' => '12345']))
            ->assertOk()
            ->assertSeeText('No patients found');
    }

    public function testSearchSqlInjectionWhenSqliOff(): void
    {
        $this->user->update(['sqli_on' => false]);

        $this->get(route('patients.index', ['search' => "' OR 1=1; --"]))
            ->assertOk()
            ->assertSeeText('No patients found');
    }

    public function testSearchWithLongInput(): void
    {
        $longInput = str_repeat('A', 1000);

        $this->get(route('patients.index', ['search' => $longInput]))
            ->assertOk()
            ->assertSeeText('No patients found');
    }
}
