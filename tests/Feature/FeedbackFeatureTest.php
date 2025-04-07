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

    public function testAuthorizedFeedbackRoute(): void
    {
        $this->get(route('feedback'))->assertOk();
    }

    public function testUnautorizedFeedbackRoute(): void
    {
        auth()->logout();
        $this->get(route('feedback'))->assertRedirect(route('login'));
    }

    public function testStoringDataWithoutCsrfToken(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->post(route('feedback.store'), [
            'patient_id' => 1000,
            'feedback' => 'This is a test comment.'
        ])->assertCsrfMismatch();
    }

    public function testStoringFeedbackData(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.')
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSeeText('This is a test comment.');
    }

    public function testStoreWithSanitizingFeedbackData(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('<script>alert("hello");</script>')
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSeeText('&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', false);
    }

    public function testStoreWithUnsanitizedFeedbackData(): void
    {
        $this->user->update(['xss_stored_on' => true]);
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('<script>alert("hello");</script>')
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSee('<script>alert("hello");</script>', false);
    }

    public function testStoringInvalidUserId(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.', 88)
            ->assertRedirect(route('feedback'));

        $this->get(route('feedback'))
            ->assertSeeText('The selected patient id is invalid.');
    }

    public function testSearchingDataWithoutCsrfToken(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->post(route('feedback.search'), [
            'search_name' => 'FooBar'
        ])->assertCsrfMismatch();
    }

    public function testSearchWithValidUsername(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.')
            ->assertRedirect(route('feedback'));

        $this->sendTestSearchFeedback($this->patient->first_name)
            ->assertOk()
            ->assertSeeText('This is a test comment.');
    }

    public function testSearchWithUnknownPatientName(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestStoreFeedback('This is a test comment.')
            ->assertRedirect(route('feedback'));

        $this->sendTestSearchFeedback('FooBar')
            ->assertOk()
            ->assertDontSeeText('This is a test comment.');
    }

    public function testSearchWithSanitizedInput(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestSearchFeedback('<script>alert("hello");</script>')
            ->assertOk()
            ->assertSeeText('&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', false);
    }

    public function testSearchWithUnsanitizedInput(): void
    {
        $this->user->update(['xss_reflected_on' => true]);
        $this->get(route('feedback'))->assertOk();

        $this->sendTestSearchFeedback('<script>alert("hello");</script>')
            ->assertOk()
            ->assertSee('<script>alert("hello");</script>', false);
    }

    public function testSeeAllPostsRedirectButton(): void
    {
        $this->get(route('feedback'))->assertOk();

        $this->sendTestSearchFeedback('FooBar')
            ->assertOk()
            ->assertSeeText('See All Posts')
            ->assertSee(route('feedback'));
    }
}
