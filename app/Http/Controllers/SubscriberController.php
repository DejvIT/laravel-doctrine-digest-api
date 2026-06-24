<?php

namespace App\Http\Controllers;

use App\EntityRepositories\SubscriberRepository;
use App\Http\Requests\ListSubscribersRequest;
use App\Http\Resources\SubscriberResource;
use Illuminate\Http\JsonResponse;

class SubscriberController extends Controller
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
    ) {
    }

    public function index(ListSubscribersRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->subscriberRepository->list(
            $validated['category_uuid'] ?? null,
            $validated['email'] ?? null,
            (int) ($validated['page'] ?? 1),
            (int) ($validated['per_page'] ?? 50)
        );

        return $this->paginatedResponse($result, SubscriberResource::toArray(...));
    }
}
