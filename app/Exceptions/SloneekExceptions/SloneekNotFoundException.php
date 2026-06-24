<?php

namespace App\Exceptions\SloneekExceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class SloneekNotFoundException extends HttpException
{
    public function __construct(string $message = null, Throwable $previous = null)
    {
        parent::__construct(
            Response::HTTP_NOT_FOUND,
            $message ?? __('be.responses.notFound'),
            $previous
        );
    }

}
