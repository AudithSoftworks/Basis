<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Inspiring;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('inspire')->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        /*-------------------------------------------------------------------------
         | This is where you may define all of your Closure based console
         | commands. Each Closure is bound to a command instance allowing a
         | simple approach to interacting with each command's IO methods.
         *------------------------------------------------------------------------*/

        app(\Illuminate\Contracts\Console\Kernel::class)->command('inspire', function () {
            /** @var \Illuminate\Console\Command $this */
            $this->comment(Inspiring::quote());
        });
    }
}