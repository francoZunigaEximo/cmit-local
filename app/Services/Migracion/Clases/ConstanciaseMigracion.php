<?php

namespace App\Services\Migration\Clases;

use App\Models\Autorizado;
use App\Models\Cliente;
use App\Models\Constanciase;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ConstanciaseMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $constanciase = Constanciase::firstOrCreate('Id', $data['Id']);

        if($constanciase->exists) {
            $default = [
                'NroC' => $data['NroC'],
                'Fecha' => $data['Fecha'],
                'Obs' => $data['Obs']
            ];

            $constanciase->fill($default);
        }

        $constanciase->fill($data);
        $constanciase->save();
        Log::info("Constanciase {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Constanciase::where('Id', $before['Id'])->delete();
        Log::info("Constanciase {$before['Id']} eliminado.");
    }

}