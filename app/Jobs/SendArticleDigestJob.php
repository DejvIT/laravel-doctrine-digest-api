<?php

namespace App\Jobs;

use App\EntityRepositories\ArticleRepository;
use App\EntityRepositories\SubscriberRepository;
use App\Mail\ArticleDigestMail;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendArticleDigestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private string $subscriberUuid,
        private string $cutoffIso8601,
    ) {
    }

    public function handle(
        ArticleRepository $articleRepository,
        SubscriberRepository $subscriberRepository,
    ): void
    {
        $cutoff = new DateTime($this->cutoffIso8601);
        $articles = $articleRepository->findUndistributedForSubscriber($this->subscriberUuid, $cutoff);

        if ($articles === []) {
            return;
        }

        $subscriber = $subscriberRepository->get($this->subscriberUuid);

        Mail::to($subscriber->getEmail())->send(new ArticleDigestMail($subscriber, $articles));

        $uuids = array_map(fn ($article) => $article->getUuid(), $articles);
        $articleRepository->markDistributed($uuids, new DateTime());
    }
}
