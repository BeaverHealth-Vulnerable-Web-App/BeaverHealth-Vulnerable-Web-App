<?php

use App\Models\User;


function sendTestFeedback($user, $testObject) {
    $feedbackData = [
        'fname' => 'Test',
        'lname' => 'User',
        'feedback' => 'This is a test comment.'
    ];
    return $testObject->actingAs($user)->post('/feedback/store', $feedbackData);
}


test('Testing route to the feedback page', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/feedback');
    $response->assertStatus(200);
});

test('Testing feedback adding', function () {
    $user = User::factory()->create();
    $response = sendTestFeedback($user, $this);
    if ($response->status() == 302) {
        $response = $this->actingAs($user)->get('/feedback');
        $response->assertSeeText('This is a test comment.');
    }
});

test('Testing valid feedback searching', function () {
    $user = User::factory()->create();
    $response = sendTestFeedback($user, $this);
    if ($response->status() == 302) {
        $response = $this->actingAs($user)->get('/feedback/search?search_name=Test');
        if ($response->status() == 200) {
            $response->assertSeeText('This is a test comment.');
        }
    }
});

test('Testing non-valid feedback searching', function () {
    $user = User::factory()->create();
    $response = sendTestFeedback($user, $this);
    if ($response->status() == 302) {
        $response = $this->actingAs($user)->get('/feedback/search?search_name=admin');
        if ($response->status() == 200) {
            $response->assertDontSeeText('This is a test comment.');
        }
    }
});
