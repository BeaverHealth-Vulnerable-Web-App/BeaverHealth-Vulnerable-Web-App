<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PatientInfoFeatureTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['view_patient_info' => true]);
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
        $user = User::factory()->create(['view_patient_info' => true]);
        $this->actingAs($user);

        $patient = \App\Models\Patient::create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'date_of_birth' => '1985-04-01',
            'policy_number' => 'ABC123',
            'address' => '123 Main St',
            'is_employee' => false,
            'ssn' => '123-45-6789',
        ]);

        $this->get(route('patients.index', ['search' => 'John']))
            ->assertOk()
            ->assertSeeText('John Smith')
            ->assertSeeText('Policy Number');
    }


    public function testSearchHandlesInvalidInput(): void
    {
        $this->user->update(['sqli_on' => true]);
        $this->actingAs($this->user);

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
        $this->get(route('patients.index'))->assertRedirect(route('login'));
    }

    public function testAccessDeniedWithoutPermission(): void
    {
        $user = User::factory()->create(['view_patient_info' => false]);
        $this->actingAs($user);

        $response = $this->get(route('patients.index'));
        $response->assertFound();
        $response->assertSessionHas('access-status', [
            'type' => 'error',
            'message' => 'Access denied: You do not have permission to view this page.'
        ]);
    }
    public function testEmptySearchShowsNoResults(): void
    {
        $user = User::factory()->create(['view_patient_info' => true]);
        $this->actingAs($user);

        $this->get(route('patients.index', ['search' => '']))
            ->assertOk()
            ->assertDontSeeText('Policy Number');
    }
    public function testSearchWithNoResults(): void
    {
        $user = User::factory()->create(['view_patient_info' => true]);
        $this->actingAs($user);

        $this->get(route('patients.index', ['search' => 'NoSuchName']))
            ->assertOk()
            ->assertSeeText('No patients found');
    }
    public function testPatientSearchShowsDetailsLink(): void
    {
        $user = User::factory()->create(['view_patient_info' => true]);
        $this->actingAs($user);

        $patient = \App\Models\Patient::create([
            'first_name' => 'Alex',
            'last_name' => 'Test',
            'date_of_birth' => '1990-01-01',
            'policy_number' => 'XYZ999',
            'address' => '456 Test Ave',
            'is_employee' => false,
            'ssn' => '111-22-3333',
        ]);

        $this->get(route('patients.index', ['search' => 'Alex']))
            ->assertOk()
            ->assertSeeText('View Details')
            ->assertSee(route('patients.info', ['id' => $patient->patient_id]));
    }
}
