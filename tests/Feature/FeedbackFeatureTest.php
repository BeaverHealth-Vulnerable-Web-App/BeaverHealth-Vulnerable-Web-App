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
        session()->start();
    }

    public function sendTestFeedback()
    {
        $feedbackData = [
            'fname' => 'Test',
            'lname' => 'User',
            'feedback' => 'This is a test comment.',
            '_token' => session()->token()
        ];
        return $this->post('/feedback/store', $feedbackData);
    }


    public function test_route_feedback()
    {
        $response = $this->get('/feedback');
        $response->assertStatus(200);
    }

    public function test_feedback_add()
    {
        $response = $this->sendTestFeedback($this);
        $response->assertStatus(302);
        $response = $this->get('/feedback');
        $response->assertSeeText('This is a test comment.');
    }

    public function test_valid_feedback_search()
    {
        $response = $this->sendTestFeedback();
        $response->assertStatus(302);
        $response = $this->get('/feedback/search?search_name=Test');
        $response->assertStatus(200);
        $response->assertSeeText('This is a test comment.');
    }

    public function test_invalid_feedback_search()
    {
        $response = $this->sendTestFeedback();
        $response->assertStatus(302);
        $response = $this->get('/feedback/search?search_name=admin');
        $response->assertStatus(200);
        $response->assertDontSeeText('This is a test comment.');
    }
}
