<?php

namespace App\Services\Migration\Clases;

use App\Models\Proveedor;
use App\Models\RelacionGrupoCliente;
use App\Models\RelacionPaqueteFacturacion;
use App\Models\Reporte;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ReporteMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $reporte = Reporte::firstOrCreate('Id', $data['Id']);

        if($reporte->exists) {
            $default = [
                'Nombre'=>$data['Nombre'],
                'IdReporte'=>$data['IdReporte'],
                'Inactivo'=>$data['Inactivo'],
                'VistaPrevia'=>$data['VistaPrevia']
            ];

            $reporte->fill($default);
        }

        $reporte->fill($data);
        $reporte->save();
        Log::info("Reporte {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Reporte::where('Id', $before['Id'])->delete();
        Log::info("Reporte {$before['Id']} eliminado.");
    }

}