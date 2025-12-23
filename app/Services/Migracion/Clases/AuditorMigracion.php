<?php

namespace App\Services\Migration\Clases;

use App\Models\ArchivoInformador;
use App\Models\ArchivoPrestacion;
use App\Models\Auditor;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class AuditorMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $auditor = Auditor::firstOrCreate('Id', $data['Id']);

        if($auditor->exists) {
            $default = [
                'IdTabla'=> $data['IdTabla'],
                'IdAccion'=> $data['IdAccion'],
                'IdRegistro'=> $data['IdRegistro'],
                'IdUsuario'=> $data['IdUsuario'],
                'Fecha'=> $data['Fecha'],
                'Observaciones'=> $data['Observaciones']
            ];

            $auditor->fill($default);
        }

        $auditor->fill($data);
        $auditor->save();
        Log::info("Auditor {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Auditor::where('Id', $before['Id'])->delete();
        Log::info("Auditor {$before['Id']} eliminado.");
    }

}