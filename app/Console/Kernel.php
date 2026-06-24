<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }


    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('articles:dispatch-digests')
            ->dailyAt('11:00')
            ->timezone(config('app.timezone'));

        $schedule->command('articles:dispatch-digests')
            ->dailyAt('17:00')
            ->timezone(config('app.timezone'));
    }

}
