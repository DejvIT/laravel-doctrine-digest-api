<?php

namespace App\Exceptions\SloneekExceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SloneekUnauthorizedException extends HttpException
{
    public function __construct(string $message = null, Throwable $previous = null)
    {
        parent::__construct(
            Response::HTTP_UNAUTHORIZED,
            $message ?? __('be.responses.auth.invalidToken'),
            $previous
        );
    }

}
