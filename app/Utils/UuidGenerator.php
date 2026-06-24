<?php

namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * UUID generator for the Doctrine ORM.
 */
class UuidGenerator extends AbstractIdGenerator
{
    /**
     * Generate an identifier
     *
     * @param object | null $entity
     *
     * @throws Exception
     */
    public function generateId(EntityManagerInterface $em, $entity): UuidInterface
    {
        return Uuid::uuid4();
    }

}