<?php

namespace App\Console\Commands;

use App\EntityRepositories\ArticleRepository;
use App\EntityRepositories\SubscriberRepository;
use App\Jobs\FinalizeArticleDistributionJob;
use App\Jobs\SendArticleDigestJob;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;

class DispatchArticleDigestsCommand extends Command
{
    protected $signature = 'articles:dispatch-digests {--cutoff=}';

    protected $description = 'Dispatch article digest jobs for subscribers with matching undistributed articles';

    public function handle(
        ArticleRepository $articleRepository,
        SubscriberRepository $subscriberRepository,
    ): int
    {
        $cutoff = $this->resolveCutoff($this->option('cutoff'));

        $articles = $articleRepository->findUndistributedBefore($cutoff);
        if ($articles === []) {
            $this->info('No undistributed articles before cutoff. Nothing to dispatch.');

            return self::SUCCESS;
        }

        $cutoffIso = $cutoff->format(DateTime::ATOM);
        $jobs = [];

        foreach ($subscriberRepository->iterateWithUndistributedArticlesBefore($cutoff, 500) as $subscriberUuid) {
            $jobs[] = new SendArticleDigestJob($subscriberUuid, $cutoffIso);
        }

        if ($jobs === []) {
            FinalizeArticleDistributionJob::dispatch($cutoffIso);
            $this->info('No subscribers matched undistributed articles. Articles marked as distributed.');

            return self::SUCCESS;
        }

        Bus::batch($jobs)
            ->name('article-digest-distribution')
            ->then(fn () => FinalizeArticleDistributionJob::dispatch($cutoffIso))
            ->dispatch();

        $this->info(sprintf(
            'Dispatched %d digest jobs for cutoff %s.',
            count($jobs),
            $cutoff->format('Y-m-d H:i:s')
        ));

        return self::SUCCESS;
    }

    private function resolveCutoff(?string $cutoffOption): DateTime
    {
        if ($cutoffOption !== null && $cutoffOption !== '') {
            return new DateTime($cutoffOption);
        }

        $now = Carbon::now(config('app.timezone'));
        $slot11 = $now->copy()->startOfDay()->setTime(11, 0, 0);
        $slot17 = $now->copy()->startOfDay()->setTime(17, 0, 0);

        if ($now->lt($slot11)) {
            return $slot17->copy()->subDay()->toDateTime();
        }

        if ($now->lt($slot17)) {
            return $slot11->toDateTime();
        }

        return $slot17->toDateTime();
    }
}
