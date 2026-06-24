<?php

namespace App\Entities;

use App\EntityRepositories\ArticleCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleCategoryRepository::class)
 * @ORM\Table(name="article_categories")
 */
class ArticleCategory extends BaseEntity
{
    /** @ORM\Column(type="string", length=255, unique=true) */
    private string $name;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $description = null;

    /**
     * @var Collection<int, Article>
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="category")
     */
    private Collection $articles;

    /**
     * @var Collection<int, Blogger>
     * @ORM\ManyToMany(targetEntity=Blogger::class, mappedBy="categories")
     */
    private Collection $bloggers;

    /**
     * @var Collection<int, Subscriber>
     * @ORM\ManyToMany(targetEntity=Subscriber::class, mappedBy="categories")
     */
    private Collection $subscribers;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->bloggers = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @return Collection<int, Blogger>
     */
    public function getBloggers(): Collection
    {
        return $this->bloggers;
    }

    /**
     * @return Collection<int, Subscriber>
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }
}
