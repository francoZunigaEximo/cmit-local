<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\FacturaCompra;
use App\Models\FacturaDeVenta;
use App\Models\FacturaResumen;
use App\Services\Reportes\Cuerpos\Factura;
use Illuminate\Support\Facades\Log;

class FacturaResumenMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $facturaResumen = FacturaResumen::firstOrNew(['Id' => $data['Id']]);

        if (!$facturaResumen->exists) {

            $default = [
                'IdFactura' => $data['IdFactura'],
                'Total' => $data['Total'],
                'Detalle' => $data['Detalle'],
                'Cod' => $data['Cod']
            ];

            $facturaResumen->fill($default);
        }

        $facturaResumen->fill($data);
        $facturaResumen->save();
        Log::info("Factura Resumen {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        FacturaResumen::where('Id', $before['Id'])->delete();
        Log::info("Factura Resumen {$before['Id']} eliminado.");
    }

}