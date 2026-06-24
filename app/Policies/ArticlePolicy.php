<?php

namespace App\Policies;

use App\Entities\Article;
use App\Entities\ArticleCategory;
use App\Entities\Blogger;
use App\Exceptions\SloneekExceptions\SloneekForbiddenException;

class ArticlePolicy
{
    public function viewAny(Blogger $blogger): bool
    {
        return true;
    }

    public function view(Blogger $blogger, Article $article): bool
    {
        $this->assertOwner($blogger, $article);

        return true;
    }

    public function create(Blogger $blogger, ArticleCategory $category): bool
    {
        foreach ($blogger->getCategories() as $assigned) {
            if ($assigned->getUuid() === $category->getUuid()) {
                return true;
            }
        }

        throw new SloneekForbiddenException(__('be.responses.forbidden.category'));
    }

    public function update(Blogger $blogger, Article $article): bool
    {
        $this->assertOwner($blogger, $article);
        $this->assertNotDistributed($article);

        return true;
    }

    public function delete(Blogger $blogger, Article $article): bool
    {
        $this->assertOwner($blogger, $article);
        $this->assertNotDistributed($article);

        return true;
    }

    private function assertOwner(Blogger $blogger, Article $article): void
    {
        if ($article->getBlogger()->getUuid() !== $blogger->getUuid()) {
            throw new SloneekForbiddenException(__('be.responses.forbidden.notOwner'));
        }
    }

    private function assertNotDistributed(Article $article): void
    {
        if ($article->isDistributed()) {
            throw new SloneekForbiddenException(__('be.responses.forbidden.distributed'));
        }
    }
}
