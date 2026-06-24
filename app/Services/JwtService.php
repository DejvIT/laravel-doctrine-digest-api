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
        $ttl = config('jwt.ttl');

        $payload = [
            'sub'   => $blogger->getUuid(),
            'email' => $blogger->getEmail(),
            'iat'   => $now,
            'exp'   => $now + $ttl,
        ];

        return JWT::encode($payload, config('jwt.secret'), config('jwt.algorithm'));
    }

    public function validateToken(string $token): Blogger
    {
        try {
            $payload = JWT::decode($token, new Key(config('jwt.secret'), config('jwt.algorithm')));
        } catch (Throwable $e) {
            throw new SloneekUnauthorizedException(__('be.responses.auth.invalidToken'), $e);
        }

        if (!isset($payload->sub) || !is_string($payload->sub)) {
            throw new SloneekUnauthorizedException(__('be.responses.auth.invalidToken'));
        }

        return $this->bloggerRepository->getWithCategories($payload->sub);
    }
}
