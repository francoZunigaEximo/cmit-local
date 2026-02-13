<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\GrupoClientes;
use App\Models\HistorialDatoPaciente;
use App\Models\HistorialPrestacion;
use Illuminate\Support\Facades\Log;

class HistorialPrestacionMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $historialPrestacion = HistorialPrestacion::firstOrNew(['Id' => $data['Id']]);

        if (!$historialPrestacion->exists) {

            $default = [
               'IdPaciente'=>$data['IdPaciente'],
                'IdEmpresa'=>$data['IdEmpresa'],
                'IdART'=>$data['IdART'],
                'TipoPrestacion'=>$data['TipoPrestacion'],
                'Fecha'=>$data['Fecha'],
                'Anulado'=>$data['Anulado'],
                'Cerrado'=>$data['Cerrado'],
                'Entregado'=>$data['Entregado'],
                'Facturado'=>$data['Facturado'],
                'ObsAnulado'=>$data['ObsAnulado'],
                'Evaluacion'=>$data['Evaluacion'],
                'Calificacion'=>$data['Calificacion'],
                'Observaciones'=>$data['Observaciones'],
                'NumeroFacturaVta'=>$data['NumeroFacturaVta'],
                'FechaCierre'=>$data['FechaCierre'],
                'FechaEntrega'=>$data['FechaEntrega'],
                'FechaFact'=>$data['FechaFact'],
                'FechaAnul'=>$data['FechaAnul'],
                'Finalizado'=>$data['Finalizado'],
                'FechaFinalizado'=>$data['FechaFinalizado'],
                'ObsExamenes'=>$data['ObsExamenes'],
                'Vto'=>$data['Vto'],
                'FechaVto'=>$data['FechaVto'],
                'NroCEE'=>$data['NroCEE'],
                'Pago'=>$data['Pago'],
                'SPago'=>$data['SPago'],
                'TSN'=>$data['TSN'],
                'FechaT'=>$data['FechaT'],
                'Incompleto'=>$data['Incompleto'],
                'AutorizaSC'=>$data['AutorizaSC'],
                'RxPreliminar'=>$data['RxPreliminar'],
            ];

            $historialPrestacion->fill($default);
        }

        $historialPrestacion->fill($data);
        $historialPrestacion->save();
        Log::info("Historial Prestacion {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        HistorialPrestacion::where('Id', $before['Id'])->delete();
        Log::info("Historial Prestacion {$before['Id']} eliminado.");
    }

}