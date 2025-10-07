<?php

namespace App\Console\Commands;

use App\Models\UserSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

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
       $sesionesInactivas = UserSession::where('last_heartbeat_at', '<', $tiempo)->whereNull('logout_at')->get();

       if($sesionesInactivas->isEmpty()) {
            $this->info("No se encontraron sesiones zombies o inactivas");
            return;
       }

       foreach($sesionesInactivas as $usuario) {
            if($usuario->session_id) {
                
                $keyRedis = 'cmit:' . $usuario->session_id;
                Redis::del($keyRedis);

                $usuario->update(['logout_at' => now()]);
            }
       }        

        $this->info("Sesiones zombies eliminadas");
    }
}
