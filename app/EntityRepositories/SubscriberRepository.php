<?php

namespace App\EntityRepositories;

use App\Entities\Subscriber;
use App\Exceptions\SloneekExceptions\SloneekNotFoundException;
use Doctrine\ORM\EntityRepository;
use Generator;

class SubscriberRepository extends EntityRepository
{
    public static function make(): self
    {
        /** @var self $repository */
        $repository = app('em')->getRepository(Subscriber::class);

        return $repository;
    }

    public function get(string $uuid): Subscriber
    {
        /** @var Subscriber|null $entity */
        $entity = $this->find($uuid);
        if (!$entity) {
            throw new SloneekNotFoundException(__('be.responses.notFound.subscriber'));
        }

        return $entity;
    }

    /**
     * @return array{items: list<Subscriber>, total: int, page: int, per_page: int}
     */
    public function list(?string $categoryUuid, ?string $email, int $page, int $perPage): array
    {
        $qb = $this->createQueryBuilder('s');

        if ($categoryUuid !== null && $categoryUuid !== '') {
            $qb->innerJoin('s.categories', 'c')
                ->andWhere('c.uuid = :categoryUuid')
                ->setParameter('categoryUuid', $categoryUuid);
        }

        if ($email !== null && $email !== '') {
            $qb->andWhere('LOWER(s.email) LIKE LOWER(:email)')
                ->setParameter('email', '%' . $email . '%');
        }

        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(DISTINCT s.uuid)')->getQuery()->getSingleScalarResult();

        $items = $qb->select('s')
            ->distinct()
            ->orderBy('s.email', 'ASC')
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

    public function iterateAll(int $chunkSize): Generator
    {
        $offset = 0;

        while (true) {
            $uuids = $this->createQueryBuilder('s')
                ->select('s.uuid')
                ->orderBy('s.uuid', 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($chunkSize)
                ->getQuery()
                ->getSingleColumnResult();

            if ($uuids === []) {
                break;
            }

            foreach ($uuids as $uuid) {
                yield $uuid;
            }

            $offset += $chunkSize;
        }
    }
}
