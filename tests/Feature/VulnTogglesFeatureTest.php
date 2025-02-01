<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class VulnTogglesFeatureTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    static function toggleProvider(): array
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

    private function sendUpdateToggleRequest($toggle, $value): \Illuminate\Testing\TestResponse
    {
        return $this->postWithCsrf(
            route('vulnerability_toggles.update'),
            [
                'toggle' => $toggle,
                'value' => $value
            ]
        );
    }

    public function test_vuln_toggles_route(): void
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();
    }

    #[DataProvider('toggleProvider')]
    public function test_valid_toggle_on($toggle): void
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();

        $response = $this->sendUpdateToggleRequest($toggle, true);
        $response->assertOk();

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                $toggle => true
            ]
        );

        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();

        $crawler = new Crawler($response->content());
        $checkbox = $crawler->filter("input[type='checkbox'][id='" . $toggle . "']");

        $this->assertEquals($checkbox->attr('checked'), "");
    }

    #[DataProvider('toggleProvider')]
    public function test_valid_toggle_off($toggle): void
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();

        $response = $this->sendUpdateToggleRequest($toggle, false);
        $response->assertOk();

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                $toggle => false
            ]
        );

        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();

        $crawler = new Crawler($response->content());
        $checkbox = $crawler->filter("input[type='checkbox'][id='" . $toggle . "']");

        $this->assertEquals($checkbox->attr('checked'), null);
    }

    public function test_unauthorized_vuln_toggles_route(): void
    {
        auth()->logout();
        $response = $this->get('/vulnerability_toggles');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_vuln_toggles_update(): void
    {
        $this->startSession();
        auth()->logout();
        $response = $this->sendUpdateToggleRequest('sqli_on', true);
        $response->assertRedirect('/login');
    }

    public function test_cannot_update_others_toggles(): void
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();

        $response = $this->sendUpdateToggleRequest('sqli_on', false); // Just in case the toggle is set to true
        $response->assertOk();
        $response->assertJson(['success' => true]);

        $newUser = User::factory()->create();
        $response = $this->actingAs($newUser)->sendUpdateToggleRequest('sqli_on', true);
        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                'sqli_on' => false
            ]
        );
    }

    public function test_non_csrf_request(): void
    {
        $checkData = [
            'toggle' => 'sqli_on',
            'value' => true
        ];
        $response = $this->post(route('vulnerability_toggles.update'), $checkData);
        $response->assertCsrfMismatch();
    }

    public function test_invalid_toggle_name_request(): void
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();

        $response = $this->sendUpdateToggleRequest('invalid_toggle', true);
        $response->assertOk();
        $response->assertJson(['success' => false]);
    }

    public function test_invalid_toggle_type_request(): void
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();

        $response = $this->sendUpdateToggleRequest(1234, true);
        $response->assertUnprocessable();
        $response->assertJson(['success' => false]);
    }

    public function test_invalid_value_type_request(): void
    {
        $response = $this->get('/vulnerability_toggles');
        $response->assertOk();

        $response = $this->sendUpdateToggleRequest('sqli_on', 'invalid_type');
        $response->assertUnprocessable();
        $response->assertJson(['success' => false]);
    }
}
