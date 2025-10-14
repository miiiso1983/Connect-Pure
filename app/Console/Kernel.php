<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process recurring payments daily at 9:00 AM
        $schedule->command('recurring:process')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Send reminder for due recurring payments at 8:00 AM
        $schedule->command('recurring:process --dry-run')
            ->dailyAt('08:00')
            ->description('Check for due recurring payments');
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
