<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ParametroReporte;
use App\Models\Permiso;
use App\Models\Personal;
use App\Models\PrecioPorCodigo;
use App\Models\Prestacion;
use Illuminate\Support\Facades\Log;

class PrestacionMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $prestacion = Prestacion::firstOrNew(['Id' => $data['Id']]);

        if (!$prestacion->exists) {

            $default = [
                'Estado'=>$data['Estado'],
                'IdPaciente'=>$data['IdPaciente'],
                'IdEmpresa'=>$data['IdEmpresa'],
                'IdART'=>$data['IdART'],
                'TipoPrestacion'=>$data['TipoPrestacion'],
                'IdMapa'=>$data['IdMapa'],
                'Pago'=>$data['Pago'],
                'SPago'=>$data['SPago'],
                'Observaciones'=>$data['Observaciones'],
                'NumeroFacturaVta'=>$data['NumeroFacturaVta'],
                'Fecha'=>$data['Fecha'],
                'Financiador'=>$data['Financiador'],
                'FechaCierre'=>$data['FechaCierre'],
                'FechaFinalizado'=>$data['FechaFinalizado'],
                'Finalizado'=>$data['Finalizado'],
                'Cerrado'=>$data['Cerrado'],
                'Entregar'=>$data['Entregar'],
                'FechaEntrega'=>$data['FechaEntrega'],
                'eEnviado'=>$data['eEnviado'],
                'FechaEnviado'=>$data['FechaEnviado'],
                'FechaVto'=>$data['FechaVto'],
                'Vto'=>$data['Vto'],
                'IdEvaluador'=>$data['IdEvaluador'],
                'Devol'=>$data['Devol'],
                'Ausente'=>$data['Ausente'],
                'Forma'=>$data['Forma'],
                'SinEsc'=>$data['SinEsc'],
                'RxPreliminar'=>$data['RxPreliminar'],
                'Incompleto'=>$data['Incompleto'],
                'FechaAnul'=>$data['FechaAnul'],
                'FechaFact'=>$data['FechaFact'],
                'Evaluacion'=>$data['Evaluacion'],
                'Anulado'=>$data['Anulado'],
                'datos_facturacion_id'=>$data['datos_facturacion_id']
            ];

            $prestacion->fill($default);
        }

        $prestacion->fill($data);
        $prestacion->save();
        Log::info("Prestacion {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Prestacion::where('Id', $before['Id'])->delete();
        Log::info("Prestacion {$before['Id']} eliminada.");
    }

}