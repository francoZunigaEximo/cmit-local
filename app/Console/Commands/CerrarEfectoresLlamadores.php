<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Llamador;
use App\Models\ItemPrestacion;

class CerrarEfectoresLlamadores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cerrar-efectores-llamadores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra registros de Llamador tipo EFECTOR con mas de 5 horas y con itemsprestaciones cerradas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $vencidos = Llamador::where('start_at', '<=', now()->subMinutes(5))->where('tipo_profesional', 'EFECTOR')->get();

        if(empty($vencidos)) return;

        foreach($vencidos as $vencido) {
            $query = ItemPrestacion::where('IdPrestacion', $vencido->prestacion_id)
                ->whereNotIn('CAdj', [3,4,5])
                ->count();

            if($query === 0) {
               $vencido->delete();
            }
        }
    }
}
