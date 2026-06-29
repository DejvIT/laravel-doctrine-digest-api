<?php

namespace Tests\Feature;

use App\EntityRepositories\ArticleRepository;
use App\Mail\ArticleDigestMail;
use Database\Seeders\DistributionDemoSeeder;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DistributionTest extends TestCase
{
    public function test_dispatch_sends_digest_and_marks_articles_distributed(): void
    {
        Mail::fake();

        $this->seedDomainData();
        $article = $this->createArticle($this->blogger1, $this->categoryA);

        $cutoff = now()->addHour()->format('Y-m-d H:i:s');

        $this->artisan('articles:dispatch-digests', ['--cutoff' => $cutoff]);

        Mail::assertSent(ArticleDigestMail::class, 1);

        app('em')->clear();

        $reloaded = ArticleRepository::make()->get($article->getUuid());
        $this->assertNotNull($reloaded->getDistributedAt());
    }

    public function test_dispatch_sends_digest_to_all_subscribers_on_shared_category(): void
    {
        Mail::fake();

        $this->seed(DistributionDemoSeeder::class);

        $cutoff = now()->addHour()->format('Y-m-d H:i:s');

        $this->artisan('articles:dispatch-digests', ['--cutoff' => $cutoff]);

        Mail::assertSent(ArticleDigestMail::class, count(DistributionDemoSeeder::SUBSCRIBER_EMAILS));

        foreach (DistributionDemoSeeder::SUBSCRIBER_EMAILS as $email) {
            Mail::assertSent(ArticleDigestMail::class, function (ArticleDigestMail $mail) use ($email): bool {
                return $mail->hasTo($email) && count($mail->articles) === count(DistributionDemoSeeder::ARTICLE_TITLES);
            });
        }

        app('em')->clear();

        $articles = ArticleRepository::make()->findUndistributedBefore(new \DateTime($cutoff));
        $this->assertSame([], $articles);
    }
}
