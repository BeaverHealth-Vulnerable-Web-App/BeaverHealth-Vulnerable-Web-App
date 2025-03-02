<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Tests\TestCase;

class FeedbackFeatureTest extends TestCase
{
    protected $user;
    protected $patient;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->patient = Patient::factory()->create(['patient_id' => 1000]);
    }

    private function sendTestStoreFeedback(): \Illuminate\Testing\TestResponse
    {
        return $this->postWithCsrf(
            route('feedback.store'),
            [
                'patient_id' => $this->patient->patient_id,
                'feedback' => 'This is a test comment.'
            ]
        );
    }

    private function sendTestSearchFeedback($name): \Illuminate\Testing\TestResponse
    {
        return $this->postWithCsrf(
            route('feedback.search'),
            [
                'search_name' => $name
            ]
        );
    }

    public function testValidFeedbackRoute(): void
    {
        $this->get(route('feedback'))->assertOk();
    }

    public function testAddingValidFeedbackData(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback()
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSeeText('This is a test comment.');
    }

    public function testValidFeedbackSearch(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback()
            ->assertRedirect(route('feedback'));

        $this->sendTestSearchFeedback($this->patient->first_name)
            ->assertOk();
    }

    public function testInvalidFeedbackSearch(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback()
            ->assertRedirect(route('feedback'));

        $this->sendTestSearchFeedback('invalid_name')
            ->assertOk()
            ->assertDontSeeText('This is a test comment.');
    }
}
