<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\CerrarEfectoresLlamadores;
use App\Console\Commands\CerrarInformadoresLlamadores;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command("app:clean-zombie-sessions")->everyMinute();
        $schedule->command("app:eliminar-llamador")->everyTenMinutes();
        $schedule->command(CerrarEfectoresLlamadores::class)->everyFifteenMinutes();
        $schedule->command(CerrarInformadoresLlamadores::class)->everyFifteenMinutes();
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
