<?php

namespace App\Services\Migration\Clases;

use App\Models\ArchivoInformador;
use App\Models\ArchivoPrestacion;
use App\Models\Auditor;
use App\Models\AuditorAcciones;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class AuditorAccionesMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $auditorAccion = AuditorAcciones::firstOrCreate('Id', $data['Id']);

        if($auditorAccion->exists) {
            $default = [
                'Nombre' => $data['Nombre']
            ];

            $auditorAccion->fill($default);
        }

        $auditorAccion->fill($data);
        $auditorAccion->save();
        Log::info("Auditor Accion {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        AuditorAcciones::where('Id', $before['Id'])->delete();
        Log::info("Auditor Accion {$before['Id']} eliminado.");
    }

}