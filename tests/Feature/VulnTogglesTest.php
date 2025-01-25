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
        session()->start();
    }

    public function sendTestRequest($toggle, $value)
    {
        $checkData = [
            'toggle' => $toggle,
            'value' => $value,
            '_token' => session()->token()
        ];
        return $this->post('/vulnerability_toggles/update', $checkData);
    }

    public function test_route_to_vuln_toggles()
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
    }

    public function test_sqli_toggle_on()
    {
        $response = $this->sendTestRequest('sqli_on', true);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['SQL Injection', 'checked']);
    }

    public function test_sqli_toggle_off()
    {
        $response = $this->sendTestRequest('sqli_on', false);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['SQL Injection', '>']);
    }

    public function test_cmd_inject_toggle_on()
    {
        $response = $this->sendTestRequest('cmd_inject_on', true);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Command Injection', 'checked']);
    }

    public function test_cmd_inject_toggle_off()
    {
        $response = $this->sendTestRequest('cmd_inject_on', false);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Command Injection', '>']);
    }

    public function test_idor_toggle_on()
    {
        $response = $this->sendTestRequest('idor_on', true);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Insecure Direct Object Reference', 'checked']);
    }

    public function test_idor_toggle_off()
    {
        $response = $this->sendTestRequest('idor_on', false);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Insecure Direct Object Reference', '>']);
    }

    public function test_file_upload_toggle_on()
    {
        $response = $this->sendTestRequest('file_upload_on', true);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['File Upload', 'checked']);
    }

    public function test_file_upload_toggle_off()
    {
        $response = $this->sendTestRequest('file_upload_on', false);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['File Upload', '>']);
    }

    public function test_xss_stored_toggle_on()
    {
        $response = $this->sendTestRequest('xss_stored_on', true);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Stored Cross Site Scripting', 'checked']);
    }

    public function test_xss_stored_toggle_off()
    {
        $response = $this->sendTestRequest('xss_stored_on', false);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Stored Cross Site Scripting', '>']);
    }

    public function test_xss_reflected_toggle_on()
    {
        $response = $this->sendTestRequest('xss_reflected_on', true);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Reflected Cross Site Scripting', 'checked']);
    }

    public function test_xss_reflected_toggle_off()
    {
        $response = $this->sendTestRequest('xss_reflected_on', false);
        $response->assertStatus(200);
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Reflected Cross Site Scripting', '>']);
    }
}
