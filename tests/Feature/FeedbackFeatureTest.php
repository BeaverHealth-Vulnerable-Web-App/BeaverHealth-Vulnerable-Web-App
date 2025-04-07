<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Testing\TestResponse;
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

    private function sendTestStoreFeedback($comment): TestResponse
    {
        return $this->postWithCsrf(
            route('feedback.store'),
            [
                'patient_id' => $this->patient->patient_id,
                'feedback' => $comment
            ]
        );
    }

    private function sendTestSearchFeedback($name): TestResponse
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

    public function testAddingFeedbackData(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.')
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSeeText('This is a test comment.');
    }

    public function testAddingExploitFeedbackDataOnSecuredVersion(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('<script>alert("hello");</script>')
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSeeText('&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', false);
    }

    public function testAddingExploitFeedbackDataOnUnsecuredVersion(): void
    {
        $this->user->update(['xss_stored_on' => true]);
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('<script>alert("hello");</script>')
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSee('<script>alert("hello");</script>', false);
    }

    public function testValidUsernameSearch(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.')
            ->assertRedirect(route('feedback'));

        $this->sendTestSearchFeedback($this->patient->first_name)
            ->assertOk()
            ->assertSeeText('This is a test comment.');
    }

    public function testInvalidUsernameSearch(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.')
            ->assertRedirect(route('feedback'));

        $this->sendTestSearchFeedback('invalid_name')
            ->assertOk()
            ->assertDontSeeText('This is a test comment.');
    }
}
