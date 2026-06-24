<?php

namespace App\Http\Resources;

use App\Entities\ArticleCategory;
use DateTimeInterface;

class ArticleCategoryResource
{
    public static function toArray(ArticleCategory $category): array
    {
        return [
            'uuid'        => $category->getUuid(),
            'name'        => $category->getName(),
            'description' => $category->getDescription(),
            'created'     => $category->getCreated()->format(DateTimeInterface::ATOM),
            'updated'     => $category->getUpdated()?->format(DateTimeInterface::ATOM),
        ];
    }
}
