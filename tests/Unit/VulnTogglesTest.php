<?php

use App\Http\Controllers\VulnerabilityTogglesController;
use App\Models\User;
use Illuminate\Testing\TestView;
use Tests\TestCase;


class VulnTogglesUnitTest extends TestCase
{
    protected $controller;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new VulnerabilityTogglesController();
        $this->user = User::factory()->create();
        $this->controller->user = $this->user;
    }

    public function test_vuln_toggles_index()
    {
        $this->actingAs($this->user);

        $response = $this->controller->index();
        $this->assertInstanceOf(Illuminate\View\View::class, $response);

        $view = new TestView($response);
        $view->assertSeeText('If you want to enable a vulnerable feature, check the corresponding checkbox.');
    }

    public function test_vuln_toggles_update()
    {
        $this->actingAs($this->user);

        $request = new Illuminate\Http\Request();
        $request->merge(['toggle' => 'sqli_on', 'value' => true]);
        $response = $this->controller->update($request);
        $data = json_decode($response->getContent(), true);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(true, $this->user->sqli_on);
        $this->assertEquals(["success" => true], $data);
    }
}
