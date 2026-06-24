<?php

namespace App\EntityRepositories;

use App\Entities\Blogger;
use App\Exceptions\SloneekExceptions\SloneekNotFoundException;
use Doctrine\ORM\EntityRepository;

class BloggerRepository extends EntityRepository
{
    public static function make(): self
    {
        /** @var self $repository */
        $repository = app('em')->getRepository(Blogger::class);

        return $repository;
    }

    public function get(string $uuid): Blogger
    {
        /** @var Blogger|null $entity */
        $entity = $this->find($uuid);
        if (!$entity) {
            throw new SloneekNotFoundException(__('be.responses.notFound.blogger'));
        }

        return $entity;
    }

    public function findByEmail(string $email): ?Blogger
    {
        /** @var Blogger|null $entity */
        $entity = $this->findOneBy(['email' => $email]);

        return $entity;
    }

    public function getWithCategories(string $uuid): Blogger
    {
        /** @var Blogger|null $entity */
        $entity = $this->createQueryBuilder('b')
            ->addSelect('c')
            ->leftJoin('b.categories', 'c')
            ->where('b.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$entity) {
            throw new SloneekNotFoundException(__('be.responses.notFound.blogger'));
        }

        return $entity;
    }
}
