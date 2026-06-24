<?php

namespace App\Exceptions\SloneekExceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SloneekInvalidEnumException extends HttpException
{
    public function __construct(string $message = null, Throwable $previous = null)
    {
        parent::__construct(
            Response::HTTP_BAD_REQUEST,
            $message ?? __('be.responses.invalidEnumValue'),
            $previous
        );
    }

}
