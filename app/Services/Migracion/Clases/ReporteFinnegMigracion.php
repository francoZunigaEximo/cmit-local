<?php

namespace App\Services\Migration\Clases;

use App\Models\Proveedor;
use App\Models\RelacionGrupoCliente;
use App\Models\RelacionPaqueteFacturacion;
use App\Models\Reporte;
use App\Models\ReporteFinneg;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ReporteFinnegMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $reporte = ReporteFinneg::firstOrCreate('Id', $data['Id']);

        if($reporte->exists) {
            $default = [
                'IdFactura' => $data['IdFactura'],
                'cuit_cliente' => $data['cuit_cliente']
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
        ReporteFinneg::where('Id', $before['Id'])->delete();
        Log::info("Reporte {$before['Id']} eliminado.");
    }

}