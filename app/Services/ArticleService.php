<?php

namespace App\Services;

use App\Entities\Article;
use App\Entities\ArticleCategory;
use App\Entities\Blogger;
use Doctrine\ORM\EntityManagerInterface;

class ArticleService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function create(Blogger $blogger, ArticleCategory $category, string $title, string $content): Article
    {
        $article = new Article();
        $article->setTitle($title);
        $article->setContent($content);
        $article->setBlogger($blogger);
        $article->setCategory($category);
        $article->setDistributedAt(null);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }

    /**
     * @param array{title?: string, content?: string} $data
     */
    public function update(Article $article, array $data): Article
    {
        if (array_key_exists('title', $data)) {
            $article->setTitle($data['title']);
        }

        if (array_key_exists('content', $data)) {
            $article->setContent($data['content']);
        }

        $this->entityManager->flush();

        return $article;
    }

    public function delete(Article $article): void
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}
