<?php

namespace App\Services\Migration\Clases;

use App\Models\ArchivoInformador;
use App\Models\ArchivoPrestacion;
use App\Models\Auditor;
use App\Models\AuditoriaMail;
use App\Models\AuditoriaMailFacturacion;
use App\Models\AuditorTabla;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class AuditorTablaMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $auditor = AuditorTabla::firstOrCreate('Id', $data['Id']);

        if($auditor->exists) {
            $default = [
                'Nombre'=> $data['Nombre']
            ];

            $auditor->fill($default);
        }

        $auditor->fill($data);
        $auditor->save();
        Log::info("Auditoria Email {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        AuditorTabla::where('Id', $before['Id'])->delete();
        Log::info("Auditor Tabla {$before['Id']} eliminado.");
    }

}