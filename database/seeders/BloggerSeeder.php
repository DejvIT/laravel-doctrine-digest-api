<?php

namespace Database\Seeders;

use App\Entities\ArticleCategory;
use App\Entities\Blogger;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class BloggerSeeder extends Seeder
{
    /**
     * Default password for all seeded bloggers: "password"
     */
    public function run(): void
    {
        /** @var EntityManagerInterface $em */
        $em = app('em');
        $faker = FakerFactory::create();

        /** @var list<ArticleCategory> $categories */
        $categories = $em->getRepository(ArticleCategory::class)->findAll();

        $blogger = new Blogger();
        $blogger->setName('Blogger One');
        $blogger->setEmail('blogger1@example.com');
        $blogger->setPassword('password');
        foreach ($this->pickRandomCategories($categories, $faker->numberBetween(1, 3)) as $category) {
            $blogger->addCategory($category);
        }
        $em->persist($blogger);

        for ($i = 2; $i <= 10; $i++) {
            $blogger = new Blogger();
            $blogger->setName($faker->name());
            $blogger->setEmail($faker->unique()->safeEmail());
            $blogger->setPassword('password');
            foreach ($this->pickRandomCategories($categories, $faker->numberBetween(1, 3)) as $category) {
                $blogger->addCategory($category);
            }
            $em->persist($blogger);
        }

        $em->flush();
    }

    /**
     * @param list<ArticleCategory> $categories
     * @return list<ArticleCategory>
     */
    private function pickRandomCategories(array $categories, int $count): array
    {
        if ($categories === []) {
            return [];
        }

        $count = min($count, count($categories));
        $keys = (array) array_rand($categories, $count);

        return array_map(fn (int $key) => $categories[$key], $keys);
    }
}
