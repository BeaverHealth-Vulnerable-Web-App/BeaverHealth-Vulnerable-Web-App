<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class VulnTogglesFeatureTest extends TestCase
{
    protected $user;
    protected const HTTP_OK = 200;
    protected const CSRF_TOKEN_MISMATCH = 419;
    protected const HTTP_UNPROCESSABLE_ENTITY = 422;

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
        $response->assertStatus(self::HTTP_OK);
    }

    #[DataProvider('toggleProvider')]
    public function test_valid_toggle_on($toggle)
    {
        $response = $this->sendValidTestRequest($toggle, true);
        $response->assertStatus(self::HTTP_OK);

        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(self::HTTP_OK);

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
        $response->assertStatus(self::HTTP_OK);

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                $toggle => false
            ]
        );

        $response = $this->get('/vulnerability_toggles');
        $response->assertStatus(self::HTTP_OK);

        $crawler = new Crawler($response->content());
        $checkbox = $crawler->filter("input[type='checkbox'][id='" . $toggle . "']")->first();

        $this->assertEquals($checkbox->attr('checked'), null);
    }

    public function test_unauthorized_vuln_toggles_route()
    {
        auth()->logout();
        $response = $this->get('/vulnerability_toggles');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_vuln_toggles_update()
    {
        auth()->logout();
        $response = $this->sendValidTestRequest('sqli_on', true);
        $response->assertRedirect('/login');
    }

    public function test_cannot_update_others_toggles()
    {
        $response = $this->sendValidTestRequest('sqli_on', false); // Just in case the toggle is set to true
        $response->assertStatus(self::HTTP_OK);
        $response->assertJson(['success' => true]);

        $newUser = User::factory()->create();
        $response = $this->actingAs($newUser)->sendValidTestRequest('sqli_on', true);
        $response->assertStatus(self::HTTP_OK);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                'sqli_on' => false
            ]
        );
    }

    public function test_non_csrf_request()
    {
        $checkData = [
            'toggle' => 'sqli_on',
            'value' => true
        ];
        $response = $this->post('/vulnerability_toggles/update', $checkData);
        $response->assertStatus(self::CSRF_TOKEN_MISMATCH);
    }

    public function test_invalid_toggle_name_request()
    {
        $response = $this->sendValidTestRequest('invalid_toggle', true);
        $response->assertStatus(self::HTTP_OK);
        $response->assertJson(['success' => false]);
    }

    public function test_invalid_toggle_type_request()
    {
        $response = $this->sendValidTestRequest(1234, true);
        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['success' => false]);
    }

    public function test_invalid_value_type_request()
    {
        $response = $this->sendValidTestRequest('sqli_on', 'invalid_type');
        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['success' => false]);
    }
}
