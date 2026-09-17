<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('tools:generate-audit-period')->daily();
        $schedule->command('forecast:generate-next-year')->yearlyOn(12, 25, '00:00');
        $schedule->command('unit-quotation:expire')->dailyAt('00:01');
        $schedule->command('hr:auto-clock-out')->dailyAt('17:05');
        $schedule->command('hr:auto-clock-out')->dailyAt('23:55');
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
