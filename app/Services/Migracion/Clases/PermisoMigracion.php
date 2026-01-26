<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ParametroReporte;
use App\Models\Permiso;
use Illuminate\Support\Facades\Log;

class PermisoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $permiso = Permiso::firstOrNew(['Id' => $data['Id']]);

        if (!$permiso->exists) {

            $default = [
                'slug' => $data['slug'],
                'descripcion' => $data['descripcion'],
            ];

            $permiso->fill($default);
        }

        $permiso->fill($data);
        $permiso->save();
        Log::info("Permiso {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Permiso::where('Id', $before['Id'])->delete();
        Log::info("Permiso {$before['Id']} eliminado.");
    }

}