<?php

namespace App\Services;

use App\EntityRepositories\BloggerRepository;
use App\Entities\Blogger;
use App\Exceptions\SloneekExceptions\SloneekUnauthorizedException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class JwtService
{
    public function __construct(
        private readonly BloggerRepository $bloggerRepository,
    ) {
    }

    public function issueToken(Blogger $blogger): string
    {
        $now = time();
        $ttl = (int) env('JWT_TTL', 86400);

        $payload = [
            'sub'   => $blogger->getUuid(),
            'email' => $blogger->getEmail(),
            'iat'   => $now,
            'exp'   => $now + $ttl,
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    public function validateToken(string $token): Blogger
    {
        try {
            $payload = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        } catch (Throwable $e) {
            throw new SloneekUnauthorizedException(__('be.responses.auth.invalidToken'), $e);
        }

        if (!isset($payload->sub) || !is_string($payload->sub)) {
            throw new SloneekUnauthorizedException(__('be.responses.auth.invalidToken'));
        }

        return $this->bloggerRepository->getWithCategories($payload->sub);
    }
}
