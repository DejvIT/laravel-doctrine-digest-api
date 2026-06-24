<?php

namespace App\Http\Controllers;

use App\EntityRepositories\ArticleCategoryRepository;
use App\Http\Requests\ListArticleCategoriesRequest;
use App\Http\Resources\ArticleCategoryResource;
use Illuminate\Http\JsonResponse;

class ArticleCategoryController extends Controller
{
    public function index(ListArticleCategoriesRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = ArticleCategoryRepository::make()->list(
            $validated['name'] ?? null,
            (int) ($validated['page'] ?? 1),
            (int) ($validated['per_page'] ?? 20)
        );

        return $this->paginatedResponse($result, ArticleCategoryResource::toArray(...));
    }

    public function show(string $uuid): JsonResponse
    {
        $category = ArticleCategoryRepository::make()->get($uuid);

        return $this->successResponse(ArticleCategoryResource::toArray($category));
    }
}
