<?php

namespace App\Providers;

use App\EntityRepositories\ArticleCategoryRepository;
use App\EntityRepositories\ArticleRepository;
use App\EntityRepositories\BloggerRepository;
use App\EntityRepositories\SubscriberRepository;
use App\Entities\Article;
use App\Entities\ArticleCategory;
use App\Entities\Blogger;
use App\Entities\Subscriber;
use App\Policies\ArticlePolicy;
use App\Services\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(JwtService::class);

        $this->app->bind(EntityManagerInterface::class, fn () => app('em'));

        $this->registerRepository(ArticleRepository::class, Article::class);
        $this->registerRepository(ArticleCategoryRepository::class, ArticleCategory::class);
        $this->registerRepository(BloggerRepository::class, Blogger::class);
        $this->registerRepository(SubscriberRepository::class, Subscriber::class);
    }

    /**
     * @param class-string $repositoryClass
     * @param class-string $entityClass
     */
    private function registerRepository(string $repositoryClass, string $entityClass): void
    {
        $this->app->bind($repositoryClass, function () use ($repositoryClass, $entityClass) {
            /** @var $repositoryClass $repository */
            $repository = app('em')->getRepository($entityClass);

            return $repository;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
    }
}
