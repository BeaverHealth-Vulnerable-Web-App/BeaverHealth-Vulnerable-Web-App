<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro(
            'assertCsrfMismatch',
            function () {
                return $this->assertStatus(419);
            }
        );
    }

    protected function postWithCsrf(string $route, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->post($route, array_merge(['_token' => session()->token()], $data));
    }
}
