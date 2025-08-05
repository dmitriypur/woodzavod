<?php

namespace App\Console;

use App\Console\Commands\GenerateSitemap;
use App\Console\Commands\TestTelegramCommand;
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
        TestTelegramCommand::class,
        GenerateSitemap::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Генерация карт сайта каждый день в 3:00
        $schedule->command('sitemap:generate --force')
            ->dailyAt('03:00')
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}