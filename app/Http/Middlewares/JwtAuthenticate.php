<?php

namespace App\Http\Middlewares;

use App\Exceptions\SloneekExceptions\SloneekUnauthorizedException;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthenticate
{
    public function __construct(private JwtService $jwtService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if ($header === null || !str_starts_with($header, 'Bearer ')) {
            throw new SloneekUnauthorizedException(__('be.responses.auth.missingToken'));
        }

        $token = trim(substr($header, 7));

        if ($token === '') {
            throw new SloneekUnauthorizedException(__('be.responses.auth.missingToken'));
        }

        $blogger = $this->jwtService->validateToken($token);
        $request->attributes->set('blogger', $blogger);

        return $next($request);
    }
}
