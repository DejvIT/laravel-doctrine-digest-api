<?php

namespace App\Exceptions\SloneekExceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SloneekForbiddenException extends HttpException
{
    public function __construct(string $message = null, Throwable $previous = null)
    {
        parent::__construct(
            Response::HTTP_FORBIDDEN,
            $message ?? __('be.responses.forbidden'),
            $previous
        );
    }

}
