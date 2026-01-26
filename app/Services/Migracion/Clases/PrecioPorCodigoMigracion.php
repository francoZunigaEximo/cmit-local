<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ParametroReporte;
use App\Models\Permiso;
use App\Models\Personal;
use App\Models\PrecioPorCodigo;
use Illuminate\Support\Facades\Log;

class PrecioPorCodigoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $precioPorCodigo = PrecioPorCodigo::firstOrNew(['Id' => $data['Id']]);

        if (!$precioPorCodigo->exists) {

            $default = [
                'Cod' => $data['Cod'],
                'Precio' => $data['Precio'],
            ];

            $precioPorCodigo->fill($default);
        }

        $precioPorCodigo->fill($data);
        $precioPorCodigo->save();
        Log::info("PrecioPorCodigo {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        PrecioPorCodigo::where('Id', $before['Id'])->delete();
        Log::info("Precio por Codigo {$before['Id']} eliminado.");
    }

}