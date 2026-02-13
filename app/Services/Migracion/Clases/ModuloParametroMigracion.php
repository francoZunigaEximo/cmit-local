<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Mapa;
use App\Models\ModuloParametro;
use Illuminate\Support\Facades\Log;

class ModuloParametroMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $moduloParametro = ModuloParametro::firstOrNew(['Id' => $data['Id']]);

        if (!$moduloParametro->exists) {

            $default = [
                'nombre' => $data['nombre']
            ];

            $moduloParametro->fill($default);
        }

        $moduloParametro->fill($data);
        $moduloParametro->save();
        Log::info("ModuloParametro {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ModuloParametro::where('Id', $before['Id'])->delete();
        Log::info("Modulo Parametro {$before['Id']} eliminado.");
    }

}