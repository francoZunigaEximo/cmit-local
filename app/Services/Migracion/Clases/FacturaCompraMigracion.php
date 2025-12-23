<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\FacturaCompra;
use Illuminate\Support\Facades\Log;

class FacturaCompraMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $facturaCompra = FacturaCompra::firstOrNew(['Id' => $data['Id']]);

        if (!$facturaCompra->exists) {

            $default = [
                'Tipo' => $data['Tipo'],
                'Sucursal' => $data['Sucursal'],
                'NroFactura' => $data['NroFactura'],
                'Fecha' => $data['Fecha'],
                'Anulada' => $data['Anulada'],
                'FechaAnulada' => $data['FechaAnulada'],
                'IdProfesional' => $data['IdProfesional'],
                'ObsAnulado' => $data['ObsAnulado'],
                'Obs' => $data['Obs'],
                'Baja' => $data['Baja']
            ];

            $facturaCompra->fill($default);
        }

        $facturaCompra->fill($data);
        $facturaCompra->save();

        Log::info("Factura Compra {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        FacturaCompra::where('Id', $before['Id'])->delete();
        Log::info("Factura Compra {$before['Id']} eliminado.");
    }

}