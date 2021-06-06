<?php

namespace App\Console;

use App\Console\Commands\CrawlCategory;
use App\Console\Commands\CrawlComment;
use App\Console\Commands\CrawlProduct;
use App\Console\Commands\CrawlShop;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CrawlShop::class,
        CrawlProduct::class,
        CrawlComment::class,
        CrawlCategory::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('crawl:shop')->dailyAt('00:00');
        $schedule->command('crawl:product')->dailyAt('00:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
