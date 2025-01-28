<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function postWithCsrf(string $route, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->post($route, array_merge(['_token' => session()->token()], $data));
    }
}
