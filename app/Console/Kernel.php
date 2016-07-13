<?php

namespace App\Console;

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
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\GetServicePeriode::class,
        \App\Console\Commands\GetServiceFakultas::class,
        \App\Console\Commands\GetServiceProdi::class,
        \App\Console\Commands\GetServiceProgram::class,
        \App\Console\Commands\GetServiceMatkul::class,
        \App\Console\Commands\GetServiceMatkulDosen::class,
        \App\Console\Commands\GetServiceMatkulMahasiswa::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        // $schedule->command('getMatkul 338 40 1')->everyMinute();
    }
}
