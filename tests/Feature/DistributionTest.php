<?php

namespace Tests\Feature;

use App\EntityRepositories\ArticleRepository;
use App\Mail\ArticleDigestMail;
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
}
