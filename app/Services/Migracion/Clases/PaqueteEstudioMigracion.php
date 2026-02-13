<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Paciente;
use App\Models\PaqueteEstudio;
use Illuminate\Support\Facades\Log;

class PaqueteEstudioMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $paqueteEstudio = PaqueteEstudio::firstOrNew(['Id' => $data['Id']]);

        if (!$paqueteEstudio->exists) {

            $default = [
                'Nombre' => $data['Nombre'],
                'Descripcion' => $data['Descripcion'],
                'Alias' => $data['Alias'],
                'Baja' => $data['Baja']
            ];

            $paqueteEstudio->fill($default);
        }

        $paqueteEstudio->fill($data);
        $paqueteEstudio->save();
        Log::info("Paquete de Estudio {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        PaqueteEstudio::where('Id', $before['Id'])->delete();
        Log::info("Paquete de Estudio {$before['Id']} eliminado.");
    }

}