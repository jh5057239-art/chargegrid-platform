<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_homepage_is_available(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('ChargeGrid');
    }

    public function test_login_and_registration_pages_are_available_to_guests(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }
}
