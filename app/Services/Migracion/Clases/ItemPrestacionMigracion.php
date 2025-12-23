<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\HistorialPrestacion;
use App\Models\ItemPrestacion;
use Illuminate\Support\Facades\Log;

class HistorialPrestacionMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $itemPrestacion = ItemPrestacion::firstOrNew(['Id' => $data['Id']]);

        if (!$itemPrestacion->exists) {

            $default = [
               'IdPrestacion'=> $data['IdPrestacion'],
                'IdExamen'=> $data['IdExamen'],
                'ObsExamen'=> $data['ObsExamen'],
                'IdProveedor'=> $data['IdProveedor'],
                'IdProfesional'=> $data['IdProfesional'],
                'IdProfesional2'=> $data['IdProfesional2'],
                'FechaPagado'=> $data['FechaPagado'],
                'FechaPagado2'=> $data['FechaPagado2'],
                'Anulado'=> $data['Anulado'],
                'Fecha'=> $data['Fecha'],
                'FechaAsignado'=> $data['FechaAsignado'],
                'Facturado'=> $data['Facturado'],
                'NumeroFacturaVta'=> $data['NumeroFacturaVta'],
                'VtoItem'=> $data['VtoItem'],
                'Honorarios'=> $data['Honorarios'],
                'NroFactCompra'=> $data['NroFactCompra'],
                'NroFactCompra2'=> $data['NroFactCompra2'],
                'Incompleto'=> $data['Incompleto'],
                'HoraAsignado'=> $data['HoraAsignado'],
                'HoraFAsignado'=> $data['HoraFAsignado'],
                'SinEsc'=> $data['SinEsc'],
                'Forma'=> $data['Forma'],
                'Ausente'=> $data['Ausente'],
                'Devol'=> $data['Devol'],
                'CInfo'=> $data['CInfo'],
                'CAdj'=> $data['CAdj'],
                'FechaAnulado'=> $data['FechaAnulado']
            ];

            $itemPrestacion->fill($default);
        }

        $itemPrestacion->fill($data);
        $itemPrestacion->save();
        Log::info("Item Prestacion {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ItemPrestacion::where('Id', $before['Id'])->delete();
        Log::info("Item Prestacion {$before['Id']} eliminado.");
    }

}