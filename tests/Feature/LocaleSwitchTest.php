<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_switch_sets_cookie_and_session(): void
    {
        $response = $this->withHeader('Referer', '/catalog')->get('/locale/en');

        $response->assertRedirect('/catalog');
        $response->assertCookie('locale', 'en');
        $response->assertSessionHas('locale', 'en');
    }
}
