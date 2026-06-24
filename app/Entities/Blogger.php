<?php

namespace App\Entities;

use App\EntityRepositories\BloggerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

/**
 * @ORM\Entity(repositoryClass=BloggerRepository::class)
 * @ORM\Table(name="bloggers")
 */
class Blogger extends BaseEntity implements Authenticatable
{
    /** @ORM\Column(type="string", length=255, unique=true) */
    private string $email;

    /** @ORM\Column(type="string", length=255) */
    private string $password;

    /** @ORM\Column(type="string", length=255) */
    private string $name;

    /**
     * @var Collection<int, ArticleCategory>
     * @ORM\ManyToMany(targetEntity=ArticleCategory::class, inversedBy="bloggers")
     * @ORM\JoinTable(
     *     name="blogger_article_category",
     *     joinColumns={@ORM\JoinColumn(name="blogger_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="article_category_uuid", referencedColumnName="uuid")}
     * )
     */
    private Collection $categories;

    /**
     * @var Collection<int, Article>
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="blogger")
     */
    private Collection $articles;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = Hash::make($password);
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
            $category->getBloggers()->add($this);
        }
    }

    public function removeCategory(ArticleCategory $category): void
    {
        if ($this->categories->removeElement($category)) {
            $category->getBloggers()->removeElement($this);
        }
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function getAuthIdentifierName(): string
    {
        return 'uuid';
    }

    public function getAuthIdentifier(): string
    {
        return $this->uuid;
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName(): ?string
    {
        return null;
    }
}
