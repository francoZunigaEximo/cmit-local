<?php

namespace App\Console\Commands;

use App\Models\UserSession;
use Illuminate\Console\Command;

class CleanZombieSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-zombie-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar las sesiones que no se han reportado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $tiempo = now()->subMinutes(2);
       UserSession::where('last_heartbeat_at', '<', $tiempo)
            ->update(['logout_at' => now()]);

        $this->info("Sesiones zombies eliminadas");
    }
}
