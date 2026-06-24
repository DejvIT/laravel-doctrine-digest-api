<?php

namespace Database\Seeders;

use App\Entities\Article;
use App\Entities\ArticleCategory;
use App\Entities\Blogger;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        /** @var EntityManagerInterface $em */
        $em = app('em');
        $faker = FakerFactory::create();

        /** @var list<Blogger> $bloggers */
        $bloggers = $em->getRepository(Blogger::class)->findAll();

        foreach ($bloggers as $blogger) {
            $categories = $blogger->getCategories()->toArray();
            if ($categories === []) {
                continue;
            }

            $articleCount = $faker->numberBetween(3, 7);

            for ($i = 0; $i < $articleCount; $i++) {
                /** @var ArticleCategory $category */
                $category = $faker->randomElement($categories);

                $article = new Article();
                $article->setTitle($faker->sentence(6));
                $article->setContent($faker->paragraphs(3, true));
                $article->setBlogger($blogger);
                $article->setCategory($category);

                if ($faker->boolean(30)) {
                    $article->setDistributedAt(DateTime::createFromInterface(
                        $faker->dateTimeBetween('-30 days', '-1 day')
                    ));
                }

                $em->persist($article);
            }
        }

        $em->flush();
    }
}
