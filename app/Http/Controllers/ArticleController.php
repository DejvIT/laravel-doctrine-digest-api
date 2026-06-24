<?php

namespace App\Http\Controllers;

use App\EntityRepositories\ArticleCategoryRepository;
use App\EntityRepositories\ArticleRepository;
use App\Entities\Article;
use App\Http\Requests\ListArticlesRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
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

        /** @var EntityManagerInterface $em */
        $em = app('em');

        $article = new Article();
        $article->setTitle($validated['title']);
        $article->setContent($validated['content']);
        $article->setBlogger($blogger);
        $article->setCategory($category);
        $article->setDistributedAt(null);

        $em->persist($article);
        $em->flush();

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

        $validated = $request->validated();

        if (array_key_exists('title', $validated)) {
            $article->setTitle($validated['title']);
        }

        if (array_key_exists('content', $validated)) {
            $article->setContent($validated['content']);
        }

        /** @var EntityManagerInterface $em */
        $em = app('em');
        $em->flush();

        return $this->successResponse(ArticleResource::toArray($article));
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $article = ArticleRepository::make()->get($uuid);
        $this->authorize('delete', $article);

        /** @var EntityManagerInterface $em */
        $em = app('em');
        $em->remove($article);
        $em->flush();

        return $this->noContentResponse();
    }
}
