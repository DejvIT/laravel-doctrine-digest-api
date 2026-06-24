<?php

namespace App\Http\Controllers;

use App\EntityRepositories\ArticleCategoryRepository;
use App\EntityRepositories\ArticleRepository;
use App\Entities\Article;
use App\Http\Requests\ListArticlesRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
    ) {
    }

    public function index(ListArticlesRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Article::class);

        $validated = $request->validated();
        $blogger = $this->currentBlogger($request);

        $result = ArticleRepository::make()->listByBlogger(
            $blogger->getUuid(),
            $validated['category_uuid'] ?? null,
            array_key_exists('distributed', $validated) ? (bool) $validated['distributed'] : null,
            (int) ($validated['page'] ?? 1),
            (int) ($validated['per_page'] ?? 20)
        );

        return $this->successResponse([
            'items'    => array_map(fn (Article $article) => ArticleResource::toArray($article), $result['items']),
            'total'    => $result['total'],
            'page'     => $result['page'],
            'per_page' => $result['per_page'],
        ]);
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $blogger = $this->currentBlogger($request);
        $category = ArticleCategoryRepository::make()->get($validated['category_uuid']);

        $this->authorize('create', [Article::class, $category]);

        $article = $this->articleService->create(
            $blogger,
            $category,
            $validated['title'],
            $validated['content']
        );

        return $this->createdResponse(ArticleResource::toArray($article));
    }

    public function show(Request $request, string $uuid): JsonResponse
    {
        $article = ArticleRepository::make()->get($uuid);
        $this->authorize('view', $article);

        return $this->successResponse(ArticleResource::toArray($article));
    }

    public function update(UpdateArticleRequest $request, string $uuid): JsonResponse
    {
        $article = ArticleRepository::make()->get($uuid);
        $this->authorize('update', $article);

        $article = $this->articleService->update($article, $request->validated());

        return $this->successResponse(ArticleResource::toArray($article));
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $article = ArticleRepository::make()->get($uuid);
        $this->authorize('delete', $article);

        $this->articleService->delete($article);

        return $this->noContentResponse();
    }
}
