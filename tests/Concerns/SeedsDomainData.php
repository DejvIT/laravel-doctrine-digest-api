<?php

namespace Tests\Concerns;

use App\Entities\Article;
use App\Entities\ArticleCategory;
use App\Entities\Blogger;
use App\Entities\Subscriber;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

trait SeedsDomainData
{
    protected ArticleCategory $categoryA;

    protected ArticleCategory $categoryB;

    protected Blogger $blogger1;

    protected Blogger $blogger2;

    protected Subscriber $subscriber;

    protected function seedDomainData(): void
    {
        /** @var EntityManagerInterface $em */
        $em = app('em');

        $this->categoryA = new ArticleCategory();
        $this->categoryA->setName('Technology');
        $this->categoryA->setDescription('Technology articles');

        $this->categoryB = new ArticleCategory();
        $this->categoryB->setName('Sports');
        $this->categoryB->setDescription('Sports articles');

        $this->blogger1 = new Blogger();
        $this->blogger1->setEmail('blogger1@example.com');
        $this->blogger1->setName('Blogger One');
        $this->blogger1->setPassword('password');
        $this->blogger1->addCategory($this->categoryA);

        $this->blogger2 = new Blogger();
        $this->blogger2->setEmail('blogger2@example.com');
        $this->blogger2->setName('Blogger Two');
        $this->blogger2->setPassword('password');
        $this->blogger2->addCategory($this->categoryB);

        $this->subscriber = new Subscriber();
        $this->subscriber->setEmail('subscriber@example.com');
        $this->subscriber->setName('Subscriber One');
        $this->subscriber->addCategory($this->categoryA);

        foreach ([$this->categoryA, $this->categoryB, $this->blogger1, $this->blogger2, $this->subscriber] as $entity) {
            $em->persist($entity);
        }

        $em->flush();
    }

    protected function createArticle(
        Blogger $blogger,
        ArticleCategory $category,
        ?DateTime $distributedAt = null,
        string $title = 'Test Article'
    ): Article {
        /** @var EntityManagerInterface $em */
        $em = app('em');

        $article = new Article();
        $article->setTitle($title);
        $article->setContent('Test article content.');
        $article->setBlogger($blogger);
        $article->setCategory($category);
        $article->setDistributedAt($distributedAt);

        $em->persist($article);
        $em->flush();

        return $article;
    }
}
