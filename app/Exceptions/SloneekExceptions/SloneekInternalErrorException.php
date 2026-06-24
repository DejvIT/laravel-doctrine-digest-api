<?php

namespace App\Exceptions\SloneekExceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SloneekInternalErrorException extends HttpException
{
    public function __construct(string $message = null, Throwable $previous = null)
    {
        parent::__construct(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $message ?? __('be.responses.internalServerError'),
            $previous
        );
    }

}
