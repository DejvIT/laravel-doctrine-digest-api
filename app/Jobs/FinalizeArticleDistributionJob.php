<?php

namespace App\Jobs;

use App\EntityRepositories\ArticleRepository;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FinalizeArticleDistributionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private string $cutoffIso8601,
    ) {
    }

    public function handle(ArticleRepository $articleRepository): void
    {
        $cutoff = new DateTime($this->cutoffIso8601);
        $articleRepository->markDistributedBeforeCutoff($cutoff, new DateTime());
    }
}
