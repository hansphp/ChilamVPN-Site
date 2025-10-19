<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicIpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.geo.ip_hash_key' => 'testing-key']);
    }

    public function testLocalizedPageRendersSuccessfully(): void
    {
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.42'])
            ->get('/en/what-is-my-ip');

        $response->assertStatus(200);
        $response->assertSee('Your connection details');
        $response->assertSee('Your IP', false);
    }

    public function testApiIpReturnsClientIp(): void
    {
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
            ->getJson('/api/ip');

        $response
            ->assertOk()
            ->assertJson([
                'ip' => '198.51.100.7',
                'version' => 'IPv4',
            ]);
    }

    public function testApiGeoReturnsStructure(): void
    {
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
            ->getJson('/api/geo');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'ip',
                'country',
                'region',
                'city',
                'lat',
                'lon',
                'accuracy_radius_km',
                'timezone',
                'asn',
            ]);
    }
}
