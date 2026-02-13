<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Fichalaboral;
use App\Models\FichaPrestacionFactura;
use Illuminate\Support\Facades\Log;

class FichaPrestacionFacturaMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $fichaPrestacionFactura = FichaPrestacionFactura::firstOrNew(['Id' => $data['Id']]);

        if (!$fichaPrestacionFactura->exists) {

            $default = [
                'prestacion_id' => $data['prestacion_id'],
                'fichalaboral_id' => $data['fichalaboral_id'],
                'Tipo' => $data['Tipo'],
                'Sucursal' => $data['Sucursal'],
                'NroFactura' => $data['NroFactura'],
                'NroFactProv' => $data['NroFactProv']
            ];

            $fichaPrestacionFactura->fill($default);
        }

        $fichaPrestacionFactura->fill($data);
        $fichaPrestacionFactura->save();
        Log::info("Ficha Prestación Factura {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        FichaPrestacionFactura::where('Id', $before['Id'])->delete();
        Log::info("Ficha Prestación Factura {$before['Id']} eliminado.");
    }

}