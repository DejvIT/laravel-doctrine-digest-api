<?php

namespace App\Entities;

use App\EntityRepositories\ArticleRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\Table(
 *     name="articles",
 *     indexes={@ORM\Index(name="idx_article_distributed_at", columns={"distributed_at"})}
 * )
 */
class Article extends BaseEntity
{
    /** @ORM\Column(type="string", length=500) */
    private string $title;

    /** @ORM\Column(type="text") */
    private string $content;

    /** @ORM\Column(type="datetime", nullable=true) */
    private ?DateTime $distributedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=Blogger::class, inversedBy="articles")
     * @ORM\JoinColumn(name="blogger_uuid", referencedColumnName="uuid", nullable=false)
     */
    private Blogger $blogger;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleCategory::class, inversedBy="articles")
     * @ORM\JoinColumn(name="article_category_uuid", referencedColumnName="uuid", nullable=false)
     */
    private ArticleCategory $category;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getDistributedAt(): ?DateTime
    {
        return $this->distributedAt;
    }

    public function setDistributedAt(?DateTime $distributedAt): void
    {
        $this->distributedAt = $distributedAt;
    }

    public function isDistributed(): bool
    {
        return $this->distributedAt !== null;
    }

    public function getBlogger(): Blogger
    {
        return $this->blogger;
    }

    public function setBlogger(Blogger $blogger): void
    {
        $this->blogger = $blogger;
    }

    public function getCategory(): ArticleCategory
    {
        return $this->category;
    }

    public function setCategory(ArticleCategory $category): void
    {
        $this->category = $category;
    }
}
