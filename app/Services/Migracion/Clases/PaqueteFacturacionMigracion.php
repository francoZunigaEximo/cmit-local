<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\PaqueteFacturacion;
use Illuminate\Support\Facades\Log;

class PaqueteFacturacionMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $paqueteFacturacion = PaqueteFacturacion::firstOrNew(['Id' => $data['Id']]);

        if (!$paqueteFacturacion->exists) {

            $default = [
                'Nombre' => $data['Nombre'],
                'Descripcion' => $data['Descripcion'],
                'CantExamenes' => $data['CantExamenes'],
                'IdGrupo' => $data['IdGrupo'],
                'IdEmpresa' => $data['IdEmpresa'],
                'Cod' => $data['Cod'],
                'Alias' => $data['Alias'],
                'Baja' => $data['Baja']
            ];

            $paqueteFacturacion->fill($default);
        }

        $paqueteFacturacion->fill($data);
        $paqueteFacturacion->save();
        Log::info("Paquete de Facturación {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        PaqueteFacturacion::where('Id', $before['Id'])->delete();
        Log::info("Paquete de Facturación {$before['Id']} eliminado.");
    }

}