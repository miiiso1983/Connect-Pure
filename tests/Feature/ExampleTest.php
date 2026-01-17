<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_home_redirects_for_guest(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }
}
