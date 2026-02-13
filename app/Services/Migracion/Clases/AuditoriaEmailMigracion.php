<?php

namespace App\Services\Migration\Clases;

use App\Models\ArchivoInformador;
use App\Models\ArchivoPrestacion;
use App\Models\Auditor;
use App\Models\AuditoriaMail;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class AuditoriaEmailMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $auditoriaEmail = AuditoriaMail::firstOrCreate('Id', $data['Id']);

        if($auditoriaEmail->exists) {
            $default = [
                'Fecha'=>$data['Fecha'],
                'Asunto'=>$data['Asunto'],
                'Detalle'=>$data['Detalle'],
                'Destinatarios'=>$data['Destinatarios']
            ];

            $auditoriaEmail->fill($default);
        }

        $auditoriaEmail->fill($data);
        $auditoriaEmail->save();
        Log::info("Auditoria Email {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        AuditoriaMail::where('Id', $before['Id'])->delete();
        Log::info("Auditoria Email {$before['Id']} eliminado.");
    }

}