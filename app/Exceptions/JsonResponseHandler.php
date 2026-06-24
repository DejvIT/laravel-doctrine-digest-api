<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class JsonResponseHandler
{
    protected array $replacements = [];


    public function handle(Throwable $exception): Response
    {
        // Convert Eloquent 500 ModelNotFoundException into a 404 NotFoundHttpException
        if ($exception instanceof ModelNotFoundException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        }

        return $this->genericResponse($exception)->withException($exception);
    }


    protected function genericResponse(Throwable $exception): Response
    {
        $replacements = $this->prepareReplacements($exception);

        $response = $this->newResponseArray();

        array_walk_recursive(
            $response,
            function (&$value) use ($exception, $replacements) {
                if (Str::startsWith($value, ':') && isset($replacements[$value])) {
                    $value = $replacements[$value];
                }
            }
        );

        $response = $this->recursivelyRemoveEmptyReplacements($response);
        if (!array_key_exists('errors', $response)) {
            $response['errors'] = [];
        }

        return new Response($response, $this->getStatusCode($exception), $this->getHeaders($exception));
    }


    protected function prepareReplacements(Throwable $exception): array
    {
        $statusCode = $this->getStatusCode($exception);

        if (!$message = $exception->getMessage()) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
        }

        $replacements = [
            ':message'     => $message,
            ':status_code' => $statusCode,
        ];

        if ($exception instanceof ValidationException) {
            $replacements[':message'] = __('be.responses.unprocessableEntity');
            $replacements[':errors'] = $exception->errors();
            $replacements[':status_code'] = $exception->status;
        }

        if ($code = $exception->getCode()) {
            $replacements[':code'] = $code;
        }

        if (config('app.debug') === true) {
            $replacements[':debug'] = [
                'line'  => $exception->getLine(),
                'file'  => $exception->getFile(),
                'class' => get_class($exception),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];

            // Attach trace of previous exception, if exists
            if (!is_null($exception->getPrevious())) {
                $currentTrace = $replacements[':debug']['trace'];

                $replacements[':debug']['trace'] = [
                    'previous' => explode("\n", $exception->getPrevious()->getTraceAsString()),
                    'current'  => $currentTrace,
                ];
            }
        }

        return array_merge($replacements, $this->replacements);
    }


    protected function recursivelyRemoveEmptyReplacements(array $input): array
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->recursivelyRemoveEmptyReplacements($value);
            }
        }

        return array_filter(
            $input,
            function ($value) {
                if (is_string($value)) {
                    return !Str::startsWith($value, ':');
                }

                return true;
            }
        );
    }


    protected function getHeaders(Throwable $exception): array
    {
        return $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];
    }


    protected function getStatusCode(Throwable $exception): int
    {
        if ($exception instanceof ValidationException) {
            return $exception->status;
        }

        return $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
    }


    protected function newResponseArray(): array
    {
        return [
            'message'     => ':message',
            'status_code' => ':status_code',
            'data'        => [],
            'errors'      => ':errors',
            'debug'       => ':debug',
        ];
    }

}