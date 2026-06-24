<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentBlogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    use AuthorizesRequests;
    use ResolvesCurrentBlogger;

    public function authorize($ability, $arguments = [])
    {
        [$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

        return Gate::forUser($this->currentBlogger(request()))
            ->authorize($ability, $arguments);
    }

    protected function jsonResponse(
        int $status,
        string $message = null,
        array $data = [],
        array $errors = []
    ): JsonResponse
    {
        $responseData = collect(
            [
                'message'     => $message,
                'status_code' => $status,
                'data'        => $data,
                'errors'      => $errors
            ]
        );

        return response()->json($responseData->toArray(), $status);
    }


    protected function successResponse(array $data = [], string $message = null): JsonResponse
    {
        return $this->jsonResponse(
            status:  Response::HTTP_OK,
            message: $message ?? __('be.responses.ok'),
            data:    $data
        );
    }


    protected function createdResponse(array $data = [], string $message = null): JsonResponse
    {
        return $this->jsonResponse(
            status:  Response::HTTP_CREATED,
            message: $message ?? __('be.responses.ok'),
            data:    $data
        );
    }


    protected function noContentResponse(): JsonResponse
    {
        return $this->jsonResponse(status: Response::HTTP_NO_CONTENT);
    }


    protected function errorResponse(string $message = null, array $errors = []): JsonResponse
    {
        return $this->jsonResponse(
            status:  Response::HTTP_BAD_REQUEST,
            message: $message ?? __('be.responses.badRequest'),
            errors:  $errors
        );
    }

}
