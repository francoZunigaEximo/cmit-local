<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\HistorialPrestacion;
use App\Models\ItemPrestacion;
use App\Models\ItemsFacturaVenta;
use App\Models\Llamador;
use App\Models\Localidad;
use Illuminate\Support\Facades\Log;

class LocalidadMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $localidad = Localidad::firstOrNew(['Id' => $data['Id']]);

        if (!$localidad->exists) {

            $default = [
            ];

            $localidad->fill($default);
        }

        $localidad->fill($data);
        $localidad->save();
        Log::info("Localidad {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Localidad::where('Id', $before['Id'])->delete();
        Log::info("Localidad {$before['Id']} eliminada.");
    }

}