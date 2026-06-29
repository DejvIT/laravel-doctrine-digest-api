<?php

namespace Database\Seeders;

use App\Entities\Article;
use App\Entities\ArticleCategory;
use App\Entities\Blogger;
use App\Entities\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Database\Seeder;

class DistributionDemoSeeder extends Seeder
{
    public const CATEGORY_NAME = 'Distribution Demo';

    public const BLOGGER_EMAIL = 'distribution.blogger@example.com';

    public const BLOGGER_PASSWORD = 'password';

    /** @var list<string> */
    public const SUBSCRIBER_EMAILS = [
        'distribution.subscriber1@example.com',
        'distribution.subscriber2@example.com',
        'distribution.subscriber3@example.com',
    ];

    /** @var list<string> */
    public const ARTICLE_TITLES = [
        'Demo article one',
        'Demo article two',
    ];

    public function run(): void
    {
        /** @var EntityManagerInterface $em */
        $em = app('em');

        if ($em->getRepository(Blogger::class)->findOneBy(['email' => self::BLOGGER_EMAIL]) !== null) {
            $this->command?->info('Distribution demo data already exists. Skipping.');

            return;
        }

        $category = new ArticleCategory();
        $category->setName(self::CATEGORY_NAME);
        $category->setDescription('Category for manual distribution testing');

        $blogger = new Blogger();
        $blogger->setName('Distribution Demo Blogger');
        $blogger->setEmail(self::BLOGGER_EMAIL);
        $blogger->setPassword(self::BLOGGER_PASSWORD);
        $blogger->addCategory($category);

        $em->persist($category);
        $em->persist($blogger);

        foreach (self::SUBSCRIBER_EMAILS as $index => $email) {
            $subscriber = new Subscriber();
            $subscriber->setName('Distribution Subscriber ' . ($index + 1));
            $subscriber->setEmail($email);
            $subscriber->addCategory($category);
            $em->persist($subscriber);
        }

        foreach (self::ARTICLE_TITLES as $title) {
            $article = new Article();
            $article->setTitle($title);
            $article->setContent('Seeded article for distribution demo.');
            $article->setBlogger($blogger);
            $article->setCategory($category);
            $em->persist($article);
        }

        $em->flush();

        $this->command?->info(sprintf(
            'Seeded distribution demo: %d subscribers, %d undistributed articles in "%s".',
            count(self::SUBSCRIBER_EMAILS),
            count(self::ARTICLE_TITLES),
            self::CATEGORY_NAME
        ));
        $this->command?->info('Login: ' . self::BLOGGER_EMAIL . ' / ' . self::BLOGGER_PASSWORD);
    }
}
