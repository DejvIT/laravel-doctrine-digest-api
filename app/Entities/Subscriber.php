<?php

namespace App\Entities;

use App\EntityRepositories\SubscriberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriberRepository::class)
 * @ORM\Table(name="subscribers")
 */
class Subscriber extends BaseEntity
{
    /** @ORM\Column(type="string", length=255, unique=true) */
    private string $email;

    /** @ORM\Column(type="string", length=255) */
    private string $name;

    /**
     * @var Collection<int, ArticleCategory>
     * @ORM\ManyToMany(targetEntity=ArticleCategory::class, inversedBy="subscribers")
     * @ORM\JoinTable(
     *     name="subscriber_article_category",
     *     joinColumns={@ORM\JoinColumn(name="subscriber_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="article_category_uuid", referencedColumnName="uuid")}
     * )
     */
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, ArticleCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(ArticleCategory $category): void
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->getSubscribers()->add($this);
        }
    }

    public function removeCategory(ArticleCategory $category): void
    {
        if ($this->categories->removeElement($category)) {
            $category->getSubscribers()->removeElement($this);
        }
    }
}
