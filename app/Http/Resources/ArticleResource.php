<?php

namespace App\Http\Resources;

use App\Entities\Article;
use App\Entities\ArticleCategory;
use App\Entities\Subscriber;
use DateTimeInterface;

class ArticleResource
{
    public static function toArray(Article $article): array
    {
        return [
            'uuid'           => $article->getUuid(),
            'title'          => $article->getTitle(),
            'content'        => $article->getContent(),
            'distributed_at' => $article->getDistributedAt()?->format(DateTimeInterface::ATOM),
            'created'        => $article->getCreated()->format(DateTimeInterface::ATOM),
            'updated'        => $article->getUpdated()?->format(DateTimeInterface::ATOM),
            'blogger_uuid'   => $article->getBlogger()->getUuid(),
            'category'       => ArticleCategoryResource::toArray($article->getCategory()),
        ];
    }
}
