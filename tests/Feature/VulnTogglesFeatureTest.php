<?php

namespace Tests\Feature;

use App\Models\User;
use \Illuminate\Testing\TestResponse;
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
            'sqli_on' => ['sqli_on'],
            'cmd_inject_on' => ['cmd_inject_on'],
            'idor_on' => ['idor_on'],
            'file_upload_on' => ['file_upload_on'],
            'xss_stored_on' => ['xss_stored_on'],
            'xss_reflected_on' => ['xss_reflected_on'],
        ];
    }

    private function sendUpdateToggleRequest($toggle, $value): TestResponse
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
        $this->get(route('vulnerability_toggles'))->assertOk();
    }

    #[DataProvider('toggleProvider')]
    public function test_valid_toggle_on($toggle): void
    {
        $this->get(route('vulnerability_toggles'))->assertOk();

        $this->sendUpdateToggleRequest($toggle, true)->assertOk();

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                $toggle => true
            ]
        );

        $response = $this->get(route('vulnerability_toggles'))
            ->assertOk();

        $crawler = new Crawler($response->content());
        $checkbox = $crawler->filter("input[type='checkbox'][id='" . $toggle . "']");

        $this->assertEquals($checkbox->attr('checked'), "");
    }

    #[DataProvider('toggleProvider')]
    public function test_valid_toggle_off($toggle): void
    {
        $this->get(route('vulnerability_toggles'))->assertOk();

        $this->sendUpdateToggleRequest($toggle, false)->assertOk();

        $this->assertDatabaseHas(
            'user',
            [
                'user_id' => $this->user->user_id,
                $toggle => false
            ]
        );

        $response = $this->get(route('vulnerability_toggles'))
            ->assertOk();

        $crawler = new Crawler($response->content());
        $checkbox = $crawler->filter("input[type='checkbox'][id='" . $toggle . "']");

        $this->assertEquals($checkbox->attr('checked'), null);
    }

    public function test_unauthorized_vuln_toggles_route(): void
    {
        auth()->logout();
        $this->get(route('vulnerability_toggles'))->assertRedirect(route('login'));
    }

    public function test_unauthenticated_vuln_toggles_update(): void
    {
        $this->startSession();
        auth()->logout();
        $this->sendUpdateToggleRequest('sqli_on', true)->assertRedirect(route('login'));
    }

    public function test_cannot_update_others_toggles(): void
    {
        $this->get(route('vulnerability_toggles'))->assertOk();

        $this->sendUpdateToggleRequest('sqli_on', false) // Just in case the toggle is set to true
            ->assertOk()
            ->assertJson(['success' => true]);

        $newUser = User::factory()->create();
        $this->actingAs($newUser)->sendUpdateToggleRequest('sqli_on', true)
            ->assertOk()
            ->assertJson(['success' => true]);

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
        $this->post(route('vulnerability_toggles.update'), $checkData)->assertCsrfMismatch();
    }

    public function test_invalid_toggle_name_request(): void
    {
        $this->get(route('vulnerability_toggles'))->assertOk();

        $this->sendUpdateToggleRequest('invalid_toggle', true)
            ->assertUnprocessable()
            ->assertJson(['success' => false, 'error' => 'Invalid toggle name']);
    }

    public function test_invalid_toggle_type_request(): void
    {
        $this->get(route('vulnerability_toggles'))->assertOk();

        $this->sendUpdateToggleRequest(1234, true)
            ->assertUnprocessable()
            ->assertJson(['success' => false]);
    }

    public function test_invalid_value_type_request(): void
    {
        $this->get(route('vulnerability_toggles'))->assertOk();

        $this->sendUpdateToggleRequest('sqli_on', 'invalid_type')
            ->assertUnprocessable()
            ->assertJson(['success' => false]);
    }
}
