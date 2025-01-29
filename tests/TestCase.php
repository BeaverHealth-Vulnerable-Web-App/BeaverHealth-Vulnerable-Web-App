<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function postWithCsrf(string $route, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->post($route, array_merge(['_token' => session()->token()], $data));
    }
}
