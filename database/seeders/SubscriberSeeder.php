<?php

namespace Database\Seeders;

use App\Entities\ArticleCategory;
use App\Entities\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class SubscriberSeeder extends Seeder
{
    public function run(): void
    {
        /** @var EntityManagerInterface $em */
        $em = app('em');

        if ($em->getRepository(Subscriber::class)->count([]) >= 100) {
            $this->command?->info('Subscribers already seeded. Skipping.');

            return;
        }

        $faker = FakerFactory::create();

        /** @var list<ArticleCategory> $categories */
        $categories = $em->getRepository(ArticleCategory::class)->findAll();

        for ($i = 0; $i < 100; $i++) {
            $subscriber = new Subscriber();
            $subscriber->setName($faker->name());
            $subscriber->setEmail($faker->unique()->safeEmail());

            $selected = $this->pickRandomCategories($categories, $faker->numberBetween(1, 3));
            foreach ($selected as $category) {
                $subscriber->addCategory($category);
            }

            $em->persist($subscriber);

            if (($i + 1) % 50 === 0) {
                $em->flush();
            }
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
