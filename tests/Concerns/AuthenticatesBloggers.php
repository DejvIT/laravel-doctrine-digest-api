<?php

namespace Tests\Concerns;

trait AuthenticatesBloggers
{
    protected function loginToken(string $email = 'blogger1@example.com', string $password = 'password'): string
    {
        $response = $this->postJson('/auth/login', [
            'email'    => $email,
            'password' => $password,
        ]);

        $response->assertOk();

        return $response->json('data.token');
    }

    protected function authHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }
}
