<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Examen;
use Illuminate\Support\Facades\Log;

class ExamenesMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $examen = Examen::firstOrNew(['Id' => $data['Id']]);

        if (!$examen->exists) {

            $default = [
                    'Nombre' => $data['Nombre'] ?? '',
                    'IdEstudio' => $data['IdEstudio'],
                    'Descripcion' => $data['Descripcion'],
                    'IdReporte' => $data['IdReporte'],
                    'IdProveedor' => $data['IdProveedor'],
                    'IdProveedor2' => $data['IdProveedor2'],
                    'DiasVencimiento' => $data['DiasVencimiento'],
                    'Inactivo' => $data['Inactivo'],
                    'IdForm' => $data['IdForm'],
                    'Cod' => $data['Cod'],
                    'Cod2' => $data['Cod2'],
                    'Ausente' => $data['Ausente'],
                    'Devol' => $data['Devol'],
                    'Informe' => $data['Informe'],
                    'Cerrado' => $data['Cerrado'],
                    'Adjunto' => $data['Adjunto'],
                    'NoImprime' => $data['NoImprime'],
                    'PI' => $data['PI'],
                    'Evaluador' => $data['Evaluador'],
                    'EvalCopia' => $data['EvalCopia'],
                    'aliasexamen' => isset($data['aliasexamen']) ? $data['aliasexamen'] : ''
                ];

            $examen->fill($default);
        }

        $examen->fill($data);
        $examen->save();

        Log::info("Examen {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Examen::where('Id', $before['Id'])->delete();
        Log::info("Examen {$before['Id']} eliminado.");
    }

}