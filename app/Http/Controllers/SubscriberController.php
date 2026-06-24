<?php

namespace App\Http\Controllers;

use App\EntityRepositories\SubscriberRepository;
use App\Entities\Subscriber;
use App\Http\Requests\ListSubscribersRequest;
use App\Http\Resources\SubscriberResource;
use Illuminate\Http\JsonResponse;

class SubscriberController extends Controller
{
    public function index(ListSubscribersRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = SubscriberRepository::make()->list(
            $validated['category_uuid'] ?? null,
            $validated['email'] ?? null,
            (int) ($validated['page'] ?? 1),
            (int) ($validated['per_page'] ?? 50)
        );

        return $this->successResponse([
            'items'    => array_map(
                fn (Subscriber $subscriber) => SubscriberResource::toArray($subscriber),
                $result['items']
            ),
            'total'    => $result['total'],
            'page'     => $result['page'],
            'per_page' => $result['per_page'],
        ]);
    }
}
