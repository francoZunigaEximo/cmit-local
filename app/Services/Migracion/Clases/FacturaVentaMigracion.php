<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\FacturaCompra;
use App\Models\FacturaDeVenta;
use App\Services\Reportes\Cuerpos\Factura;
use Illuminate\Support\Facades\Log;

class FacturaDeVentaMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $facturaVenta = FacturaDeVenta::firstOrNew(['Id' => $data['Id']]);

        if (!$facturaVenta->exists) {

            $default = [
                'Tipo' => $data['Tipo'],
                'Sucursal' => $data['Sucursal'],
                'NroFactura' => $data['NroFactura'],
                'Fecha' => $data['Fecha'],
                'Anulada' => $data['Anulada'],
                'FechaAnulada' => $data['FechaAnulada'],
                'IdEmpresa' => $data['IdEmpresa'],
                'TipoCliente' => $data['TipoCliente'],
                'ObsAnulado' => $data['ObsAnulado'],
                'EnvioFacturaF' => $data['EnvioFacturaF'],
                'Obs' => $data['Obs'],
                'IdPrestacion' => $data['IdPrestacion']
            ];

            $facturaVenta->fill($default);
        }

        $facturaVenta->fill($data);
        $facturaVenta->save();
        Log::info("Factura de Venta {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        FacturaDeVenta::where('Id', $before['Id'])->delete();
        Log::info("Factura de Venta {$before['Id']} eliminado.");
    }

}