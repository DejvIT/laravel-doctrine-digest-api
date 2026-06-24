<?php

namespace App\EntityRepositories;

use App\Entities\Article;
use App\Exceptions\SloneekExceptions\SloneekNotFoundException;
use DateTime;
use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    public static function make(): self
    {
        /** @var self $repository */
        $repository = app('em')->getRepository(Article::class);

        return $repository;
    }

    public function get(string $uuid): Article
    {
        /** @var Article|null $entity */
        $entity = $this->find($uuid);
        if (!$entity) {
            throw new SloneekNotFoundException(__('be.responses.notFound.article'));
        }

        return $entity;
    }

    /**
     * @return array{items: list<Article>, total: int, page: int, per_page: int}
     */
    public function listByBlogger(
        string $bloggerUuid,
        ?string $categoryUuid,
        ?bool $distributed,
        int $page,
        int $perPage
    ): array {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.blogger', 'b')
            ->innerJoin('a.category', 'cat')
            ->addSelect('b', 'cat')
            ->where('b.uuid = :bloggerUuid')
            ->setParameter('bloggerUuid', $bloggerUuid);

        if ($categoryUuid !== null && $categoryUuid !== '') {
            $qb->andWhere('cat.uuid = :categoryUuid')
                ->setParameter('categoryUuid', $categoryUuid);
        }

        if ($distributed === true) {
            $qb->andWhere('a.distributedAt IS NOT NULL');
        } elseif ($distributed === false) {
            $qb->andWhere('a.distributedAt IS NULL');
        }

        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(a.uuid)')->getQuery()->getSingleScalarResult();

        $items = $qb->select('a')
            ->orderBy('a.created', 'DESC')
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

    /**
     * @return list<Article>
     */
    public function findUndistributedBefore(DateTime $cutoff): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.distributedAt IS NULL')
            ->andWhere('a.created < :cutoff')
            ->setParameter('cutoff', $cutoff)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<Article>
     */
    public function findUndistributedForSubscriber(string $subscriberUuid, DateTime $cutoff): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.category', 'c')
            ->innerJoin('c.subscribers', 's')
            ->innerJoin('a.blogger', 'b')
            ->addSelect('c', 'b')
            ->where('s.uuid = :subscriberUuid')
            ->andWhere('a.distributedAt IS NULL')
            ->andWhere('a.created < :cutoff')
            ->setParameter('subscriberUuid', $subscriberUuid)
            ->setParameter('cutoff', $cutoff)
            ->orderBy('a.created', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function markDistributed(array $uuids, DateTime $distributedAt): int
    {
        if ($uuids === []) {
            return 0;
        }

        return $this->getEntityManager()->createQuery(
            'UPDATE App\Entities\Article a
             SET a.distributedAt = :distributedAt
             WHERE a.uuid IN (:uuids) AND a.distributedAt IS NULL'
        )
            ->setParameter('distributedAt', $distributedAt)
            ->setParameter('uuids', $uuids)
            ->execute();
    }
}
