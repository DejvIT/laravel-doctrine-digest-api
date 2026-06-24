<?php

namespace App\Providers;

use App\Entities\Article;
use App\Policies\ArticlePolicy;
use App\Services\JwtService;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
    }
}
