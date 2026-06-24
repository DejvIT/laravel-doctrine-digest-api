<?php

namespace App\EntityRepositories;

use App\Entities\ArticleCategory;
use App\Exceptions\SloneekExceptions\SloneekNotFoundException;
use Doctrine\ORM\EntityRepository;

class ArticleCategoryRepository extends EntityRepository
{
    public static function make(): self
    {
        return app(self::class);
    }

    public function get(string $uuid): ArticleCategory
    {
        /** @var ArticleCategory|null $entity */
        $entity = $this->find($uuid);
        if (!$entity) {
            throw new SloneekNotFoundException(__('be.responses.notFound.articleCategory'));
        }

        return $entity;
    }

    /**
     * @return array{items: list<ArticleCategory>, total: int, page: int, per_page: int}
     */
    public function list(?string $nameFilter, int $page, int $perPage): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($nameFilter !== null && $nameFilter !== '') {
            $qb->andWhere('LOWER(c.name) LIKE LOWER(:name)')
                ->setParameter('name', '%' . $nameFilter . '%');
        }

        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(c.uuid)')->getQuery()->getSingleScalarResult();

        $items = $qb->select('c')
            ->orderBy('c.name', 'ASC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }
}
