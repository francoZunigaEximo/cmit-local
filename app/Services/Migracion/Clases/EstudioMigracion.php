<?php

namespace App\Services\Migration\Clases;

use App\Models\Autorizado;
use App\Models\Cliente;
use App\Models\Constanciase;
use App\Models\EnviarModelo;
use App\Models\Estudio;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class EstudioMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $estudio = Estudio::firstOrCreate('Id', $data['Id']);

        if($estudio->exists) {
            $default = [
                'Nombre'=>$data['Nombre'],
                'Descripcion'=>$data['Descripcion']
            ];

            $estudio->fill($default);
        }

        $estudio->fill($data);
        $estudio->save();
        Log::info("Estudio {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Estudio::where('Id', $before['Id'])->delete();
        Log::info("Estudio {$before['Id']} eliminado.");
    }

}