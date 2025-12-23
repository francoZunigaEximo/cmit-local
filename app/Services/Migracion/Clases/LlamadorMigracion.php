<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\HistorialPrestacion;
use App\Models\ItemPrestacion;
use App\Models\ItemsFacturaVenta;
use App\Models\Llamador;
use Illuminate\Support\Facades\Log;

class LlamadorMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $llamador = Llamador::firstOrNew(['Id' => $data['Id']]);

        if (!$llamador->exists) {

            $default = [
                'profesional_id'=> $data['profesional_id'],
                'prestacion_id'=> $data['prestacion_id'],
                'especialidad_id'=> $data['especialidad_id'],
                'tipo_profesional'=> $data['tipo_profesional']
            ];

            $llamador->fill($default);
        }

        $llamador->fill($data);
        $llamador->save();
        Log::info("Llamador {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Llamador::where('Id', $before['Id'])->delete();
        Log::info("Llamador {$before['Id']} eliminado.");
    }

}