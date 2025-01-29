<?php

namespace Tests\Feature;

use App\Models\User;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Framework\Attributes\DataProvider;
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

    static function toggleProvider()
    {
        return [
            ['sqli_on'],
            ['cmd_inject_on'],
            ['idor_on'],
            ['file_upload_on'],
            ['xss_stored_on'],
            ['xss_reflected_on'],
        ];
    }

    public function sendValidTestRequest($toggle, $value)
    {
        $checkData = [
            'toggle' => $toggle,
            'value' => $value,
            '_token' => csrf_token(),
        ];
        return $this->post('/vulnerability_toggles/update', $checkData);
    }

    public function test_vuln_toggles_route()
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);
    }

    public function test_invalid_request_csrf()
    {
        $checkData = [
            'toggle' => 'sqli_on',
            'value' => true
        ];
        $response = $this->post('/vulnerability_toggles/update', $checkData);
        $response->assertStatus(419);
    }

    public function test_invalid_toggle_name_request()
    {
        $checkData = [
            'toggle' => 'this is a toggle',
            'value' => true
        ];
        $response = $this->post('/vulnerability_toggles/update', $checkData);
        $response->assertStatus(200);
    }

    #[DataProvider('toggleProvider')]
    public function test_valid_toggle_on($toggle)
    {
        $response = $this->sendValidTestRequest($toggle, true);
        $response->assertStatus(200);

        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                $toggle => true
            ]
        );

        $crawler = new Crawler($response->content());
        $checkbox = $crawler->filter("input[type='checkbox'][id='" . $toggle . "']")->first();

        $this->assertEquals($checkbox->attr('checked'), "");
    }

    #[DataProvider('toggleProvider')]
    public function test_valid_toggle_off($toggle)
    {
        $response = $this->sendValidTestRequest($toggle, false);
        $response->assertStatus(200);

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                $toggle => false
            ]
        );

        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(200);

        $crawler = new Crawler($response->content());
        $checkbox = $crawler->filter("input[type='checkbox'][id='" . $toggle . "']")->first();

        $this->assertEquals($checkbox->attr('checked'), null);
    }
}
