<?php

namespace App\Entities;

use App\Utils\UuidGenerator;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @ORM\Id
     */
    protected string $uuid;

    /** @ORM\Column(type="datetime") */
    protected DateTime $created;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected DateTime|null $updated = null;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected DateTime|null $removed = null;


    public function getUuid(): string
    {
        return $this->uuid;
    }


    public function getCreated(): DateTime
    {
        return $this->created;
    }


    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }


    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }


    public function setUpdated(?DateTime $updated): void
    {
        $this->updated = $updated;
    }


    public function getRemoved(): ?DateTime
    {
        return $this->removed;
    }


    public function setRemoved(?DateTime $removed): void
    {
        $this->removed = $removed;
    }


    /** @ORM\PrePersist */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setCreated(now());
    }


    /** @ORM\PreUpdate */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->setUpdated(now());
    }

}