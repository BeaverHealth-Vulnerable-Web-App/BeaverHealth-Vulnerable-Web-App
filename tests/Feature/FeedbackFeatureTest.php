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

    private function sendTestStoreFeedback($comment, $patientID = 1000): TestResponse
    {
        return $this->postWithCsrf(
            route('feedback.store'),
            [
                'patient_id' => $patientID,
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

    public function testStoreWithExploitFeedbackDataOnSecuredVersion(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('<script>alert("hello");</script>')
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSeeText('&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', false);
    }

    public function testStoreWithExploitFeedbackDataOnUnsecuredVersion(): void
    {
        $this->user->update(['xss_stored_on' => true]);
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('<script>alert("hello");</script>')
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSee('<script>alert("hello");</script>', false);
    }

    public function testInvalidUserIdWhenStoring(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.', 88)
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSeeText('The selected patient id is invalid.');
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

    public function testUnknownPatientNameSearch(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.')
            ->assertRedirect(route('feedback'));

        $this->sendTestSearchFeedback('invalid_name')
            ->assertOk()
            ->assertDontSeeText('This is a test comment.');
    }

    public function testSearchWithExploitDataOnSecuredVersion(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestSearchFeedback('<script>alert("hello");</script>')
            ->assertOk()
            ->assertSeeText('&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', false);
    }

    public function testSearchWithExploitDataOnUnsecuredVersion(): void
    {
        $this->user->update(['xss_reflected_on' => true]);
        $this->get(route('feedback'))->assertOk();

        $this->sendTestSearchFeedback('<script>alert("hello");</script>')
            ->assertOk()
            ->assertSee('<script>alert("hello");</script>', false);
    }
}
