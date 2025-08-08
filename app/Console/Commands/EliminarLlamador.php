<?php

namespace App\Console\Commands;

use App\Models\Llamador;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EliminarLlamador extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:eliminar-llamador';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina los llamados que no han sido de baja despues de 5 horas, Si tiene cerrado todos sus examenes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limite = Carbon::now()->subHours(5);
        $eliminar = Llamador::where('start_at', '<', $limite)->get();

        

        $this->info("Se eliminaron {$eliminar} registros de llamador");
    }
}
