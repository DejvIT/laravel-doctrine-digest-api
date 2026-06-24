<?php

namespace App\Exceptions;

use App\Exceptions\SloneekExceptions\SloneekForbiddenException;
use App\Exceptions\SloneekExceptions\SloneekInternalErrorException;
use App\Exceptions\SloneekExceptions\SloneekInvalidEnumException;
use App\Exceptions\SloneekExceptions\SloneekTooManyRequestsException;
use App\Exceptions\SloneekExceptions\SloneekUnauthorizedException;
use Doctrine\DBAL\Exception\ServerException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /** A list of the exception types that are not reported. */
    protected $dontReport = [
    ];


    /**
     * Render an exception into an HTTP response.
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof BackedEnumCaseNotFoundException) {
            throw new SloneekInvalidEnumException(previous: $e);
        }
        if ($e instanceof ThrottleRequestsException) {
            throw new SloneekTooManyRequestsException(previous: $e);
        }
        if ($e instanceof AuthorizationException) {
            throw new SloneekForbiddenException($e->getMessage(), $e->getPrevious());
        }
        if ($e instanceof AuthenticationException) {
            throw new SloneekUnauthorizedException();
        }
        if ($e instanceof ServerException) {
            throw new SloneekInternalErrorException();
        }

        if (!method_exists($e, 'render')) {
            $handler = new JsonResponseHandler();

            return $handler->handle($e);
        }

        return parent::render($request, $e);
    }

}
