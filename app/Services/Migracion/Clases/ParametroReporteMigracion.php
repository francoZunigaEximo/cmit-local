<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ParametroReporte;
use Illuminate\Support\Facades\Log;

class ParametroReporteMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $parametroReporte = ParametroReporte::firstOrNew(['Id' => $data['Id']]);

        if (!$parametroReporte->exists) {

            $default = [
                'titulo'=> $data['titulo'],
                'descripcion' => $data['descripcion'],
                'modulo_id' => $data['modulo_id'],
                'IdEntidad' => $data['IdEntidad'],
            ];

            $parametroReporte->fill($default);
        }

        $parametroReporte->fill($data);
        $parametroReporte->save();
        Log::info("Parámetro de Reporte {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ParametroReporte::where('Id', $before['Id'])->delete();
        Log::info("Parámetro de Reporte {$before['Id']} eliminado.");
    }

}