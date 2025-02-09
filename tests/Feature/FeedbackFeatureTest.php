<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class FeedbackFeatureTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function sendTestFeedback(): \Illuminate\Testing\TestResponse
    {
        return $this->postWithCsrf(
            route('feedback.store'),
            [
            'fname' => 'Test',
            'lname' => 'User',
            'feedback' => 'This is a test comment.'
            ]
        );
    }


    public function testRouteFeedback(): void
    {
        $response = $this->get(route('feedback'));
        $response->assertOk();
    }

    public function testFeedbackAdd(): void
    {
        $response = $this->get(route('feedback'));
        $response->assertOk();

        $response = $this->sendTestFeedback();
        $response->assertRedirect(route('feedback'));

        $response = $this->get(route('feedback'));
        $response->assertSeeText('This is a test comment.');
    }

    public function testValidFeedbackSearch(): void
    {
        $response = $this->get(route('feedback'));
        $response->assertOk();

        $response = $this->sendTestFeedback();
        $response->assertRedirect(route('feedback'));

        $response = $this->get('/feedback/search?search_name=Test');
        $response->assertOk();
        $response->assertSeeText('This is a test comment.');
    }

    public function testInvalidFeedbackSearch(): void
    {
        $response = $this->get(route('feedback'));
        $response->assertOk();

        $response = $this->sendTestFeedback();
        $response->assertRedirect(route('feedback'));

        $response = $this->get('/feedback/search?search_name=admin');
        $response->assertOk();
        $response->assertDontSeeText('This is a test comment.');
    }
}
