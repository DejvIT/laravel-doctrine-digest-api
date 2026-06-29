<?php

namespace Database\Seeders;

use App\Entities\ArticleCategory;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Database\Seeder;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        /** @var EntityManagerInterface $em */
        $em = app('em');

        if ($em->getRepository(ArticleCategory::class)->findOneBy(['name' => 'Technology']) !== null) {
            $this->command?->info('Article categories already seeded. Skipping.');

            return;
        }

        $categories = [
            ['name' => 'Technology', 'description' => 'Software, gadgets, and digital innovation'],
            ['name' => 'Sports', 'description' => 'Athletics, teams, and competitions'],
            ['name' => 'Health', 'description' => 'Wellness, medicine, and fitness'],
            ['name' => 'Finance', 'description' => 'Markets, investing, and personal finance'],
            ['name' => 'Travel', 'description' => 'Destinations, tips, and adventures'],
            ['name' => 'Science', 'description' => 'Research, discoveries, and space'],
            ['name' => 'Culture', 'description' => 'Arts, books, and entertainment'],
            ['name' => 'Food', 'description' => 'Recipes, restaurants, and cuisine'],
        ];

        foreach ($categories as $data) {
            $category = new ArticleCategory();
            $category->setName($data['name']);
            $category->setDescription($data['description']);
            $em->persist($category);
        }

        $em->flush();
    }
}
