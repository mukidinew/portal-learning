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
        $schedule->command('getService:prodi 2')->weekly()->fridays();
        $schedule->command('getService:prodi 3')->weekly()->fridays();
        $schedule->command('getService:prodi 4')->weekly()->fridays();
        $schedule->command('getService:prodi 5')->weekly()->fridays();
        $schedule->command('getService:prodi 6')->weekly()->fridays();
        $schedule->command('getService:prodi 7')->weekly()->fridays();
        $schedule->command('getService:prodi 8')->weekly()->fridays();
        $schedule->command('getService:prodi 9')->weekly()->fridays();
        $schedule->command('getService:prodi 10')->weekly()->fridays();
        $schedule->command('getService:prodi 360')->weekly()->fridays();

        $schedule->command('getService:program 2')->weekly()->fridays();
        $schedule->command('getService:program 3')->weekly()->fridays();
        $schedule->command('getService:program 4')->weekly()->fridays();
        $schedule->command('getService:program 5')->weekly()->fridays();
        $schedule->command('getService:program 6')->weekly()->fridays();
        $schedule->command('getService:program 7')->weekly()->fridays();
        $schedule->command('getService:program 8')->weekly()->fridays();
        $schedule->command('getService:program 9')->weekly()->fridays();
        $schedule->command('getService:program 10')->weekly()->fridays();
        $schedule->command('getService:program 360')->weekly()->fridays();

        $schedule->command('getService:matkul 2')->weekly()->saturdays();
        $schedule->command('getService:matkul 3')->weekly()->saturdays();
        $schedule->command('getService:matkul 4')->weekly()->saturdays();
        $schedule->command('getService:matkul 5')->weekly()->saturdays();
        $schedule->command('getService:matkul 6')->weekly()->saturdays();
        $schedule->command('getService:matkul 7')->weekly()->saturdays();
        $schedule->command('getService:matkul 8')->weekly()->saturdays();
        $schedule->command('getService:matkul 9')->weekly()->saturdays();
        $schedule->command('getService:matkul 10')->weekly()->saturdays();
        $schedule->command('getService:matkul 360')->weekly()->saturdays();

        $schedule->command('getService:matkulDosen 2')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 3')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 4')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 5')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 6')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 7')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 8')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 9')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 10')->weekly()->saturdays();
        $schedule->command('getService:matkulDosen 360')->weekly()->saturdays();

        $schedule->command('getService:matkulMahasiswa 2')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 3')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 4')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 5')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 6')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 7')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 8')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 9')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 10')->weekly()->sundays();
        $schedule->command('getService:matkulMahasiswa 360')->weekly()->sundays();
    }
}
