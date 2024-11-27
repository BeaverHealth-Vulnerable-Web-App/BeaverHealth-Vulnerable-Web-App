<?php

use App\Models\User;


function sendTestFeedback($user, $testObject) {
    $feedbackData = [
        'name' => 'Test User',
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

test('Testing feedback searching', function () {
    $user = User::factory()->create();
    $response = sendTestFeedback($user, $this);
    if ($response->status() == 302) {
        $commentData = [
            'search_name' => 'Test User'
        ];
        $response = $this->actingAs($user)->get('/feedback/search', $commentData);
        if ($response->status() == 200) {
            $response->assertSeeText('This is a test comment.');
        }
    }
});
