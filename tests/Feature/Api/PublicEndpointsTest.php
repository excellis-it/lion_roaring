<?php

namespace Tests\Feature\Api;

use Tests\Support\AssertsApiContract;
use Tests\TestCase;

class PublicEndpointsTest extends TestCase
{
    use AssertsApiContract;

    public function test_register_meta_returns_generated_id_part(): void
    {
        $response = $this->getJson('/api/v3/register-meta');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['status', 'generated_id_part']);
    }

    public function test_cms_site_settings_returns_envelope(): void
    {
        $response = $this->getJson('/api/v3/cms/site-settings');

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertArrayHasKey('message', $json);
    }

    public function test_cms_menu_returns_envelope(): void
    {
        $response = $this->getJson('/api/v3/cms/menu');

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
    }

    public function test_register_agreement_post_returns_envelope(): void
    {
        $response = $this->postJson('/api/v3/register-agreement');

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertArrayHasKey('message', $json);
    }
}
