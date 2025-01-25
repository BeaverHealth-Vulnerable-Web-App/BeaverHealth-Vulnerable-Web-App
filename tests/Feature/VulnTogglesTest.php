<?php

use App\Models\User;
use Tests\TestCase;


class VulnTogglesFeatureTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        session()->start(); // This needs to be here, or a GET request needs to be made prior to POST to start the session
    }

    public function test_function(): void
    {
        // $this->get('/vulnerability_toggles');

        $response = $this->post(
            '/vulnerability_toggles/update', [
            '_token' => session()->token(),
            'toggle' => 'sqli_on',
            'value' => true
            ]
        );

        $response->assertStatus(200);
    }

    // public function test_route_to_vuln_toggles()
    // {
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    // }

    // public function test_sqli_toggle_on()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'sqli_on',
    //         'value' => true
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['SQL Injection', 'checked']);
    // }

    // public function test_sqli_toggle_off()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'sqli_on',
    //         'value' => false
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['SQL Injection', '>']);
    // }

    // public function test_cmd_inject_toggle_on()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'cmd_inject_on',
    //         'value' => true
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['Command Injection', 'checked']);
    // }

    // public function test_cmd_inject_toggle_off()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'cmd_inject_on',
    //         'value' => false
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['Command Injection', '>']);
    // }

    // public function test_idor_toggle_()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'idor_on',
    //         'value' => true
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['Insecure Direct Object Reference', 'checked']);
    // }

    // public function test_idor_toggle_off()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'idor_on',
    //         'value' => false
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['Insecure Direct Object Reference', '>']);
    // }

    // public function test_file_upload_toggle()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'file_upload_on',
    //         'value' => true
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['File Upload', 'checked']);
    // }

    // public function test_file_upload_toggle_off()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'file_upload_on',
    //         'value' => false
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['File Upload', '>']);
    // }

    // public function test_xss_stored_toggle_on()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'xss_stored_on',
    //         'value' => true
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['Stored Cross Site Scripting', 'checked']);
    // }

    // public function test_xss_stored_toggle_off()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'xss_stored_on',
    //         'value' => false
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['Stored Cross Site Scripting', '>']);
    // }

    // public function test_xss_reflected_toggle_on()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'xss_reflected_on',
    //         'value' => true
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['Reflected Cross Site Scripting', 'checked']);
    // }

    // public function test_xss_reflected_toggle_off()
    // {
    //     $response = $this->actingAs($this->user)->post('/vulnerability_toggles/update', [
    //         'toggle' => 'xss_reflected_on',
    //         'value' => false
    //     ]);
    //     $response->assertStatus(200);
    //     $response = $this->actingAs($this->user)->get('/vulnerability_toggles');
    //     $response->assertStatus(200);
    //     $response->assertSeeInOrder(['Reflected Cross Site Scripting', '>']);
    // }
}
