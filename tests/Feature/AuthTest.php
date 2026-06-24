<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_with_valid_credentials(): void
    {
        $this->seedDomainData();

        $response = $this->postJson('/auth/login', [
            'email'    => 'blogger1@example.com',
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_with_invalid_password(): void
    {
        $this->seedDomainData();

        $response = $this->postJson('/auth/login', [
            'email'    => 'blogger1@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized();
    }

    public function test_protected_route_without_token(): void
    {
        $response = $this->getJson('/auth/me');

        $response->assertUnauthorized();
    }

    public function test_me_with_valid_token(): void
    {
        $this->seedDomainData();

        $token = $this->loginToken();

        $response = $this->getJson('/auth/me', $this->authHeaders($token));

        $response->assertOk();
        $response->assertJsonPath('data.email', 'blogger1@example.com');
        $response->assertJsonPath('data.name', 'Blogger One');
    }
}
