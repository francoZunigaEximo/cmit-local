<?php

namespace App\Services\Migration\Clases;

use App\Models\Autorizado;
use App\Models\Cliente;
use App\Models\Constanciase;
use App\Models\EnviarModelo;
use App\Models\Estudio;
use App\Models\ExamenCuenta;
use App\Models\ExamenCuentaIt;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ExamenCuentaItMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $examenIt = ExamenCuentaIt::firstOrCreate('Id', $data['Id']);

        if($examenIt->exists) {
            $default = [
                'IdPago'=>$data['IdPago'],
                'IdExamen'=>$data['IdExamen'],
                'IdPrestacion'=>$data['IdPrestacion'],
                'Obs'=>$data['Obs'],
                'Obs2'=>$data['Obs2'],
                'Obs'=>$data['Obs'],
                'Precarga'=>$data['Precarga']
            ];

            $examenIt->fill($default);
        }

        $examenIt->fill($data);
        $examenIt->save();
        Log::info("ExamenCuentaIt {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ExamenCuentaIt::where('Id', $before['Id'])->delete();
        Log::info("ExamenCuentaIt {$before['Id']} eliminado.");
    }

}