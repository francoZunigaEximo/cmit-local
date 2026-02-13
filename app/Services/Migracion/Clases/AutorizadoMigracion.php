<?php

namespace App\Services\Migration\Clases;

use App\Models\ArchivoInformador;
use App\Models\ArchivoPrestacion;
use App\Models\Auditor;
use App\Models\AuditoriaMail;
use App\Models\AuditoriaMailFacturacion;
use App\Models\AuditorTabla;
use App\Models\Autorizado;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class AutorizadoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $autorizado = Autorizado::firstOrCreate('Id', $data['Id']);

        if($autorizado->exists) {
            $default = [
                'IdEntidad' => $data['IdEntidad'], //Cliente_id
                'Nombre' => $data['Nombre'],
                'Apellido' => $data['Apellido'],
                'DNI' => $data['DNI'],
                'Derecho' => $data['Derecho'],
                'TipoEntidad' => $data['TipoEntidad'],
            ];

            $autorizado->fill($default);
        }

        $autorizado->fill($data);
        $autorizado->save();
        Log::info("Autorizado {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Autorizado::where('Id', $before['Id'])->delete();
        Log::info("Autorizado {$before['Id']} eliminado.");
    }

}