<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ParametroReporte;
use App\Models\Permiso;
use App\Models\Personal;
use App\Models\PrecioPorCodigo;
use App\Models\Prestacion;
use App\Models\PrestacionAtributo;
use App\Models\PrestacionComentario;
use App\Models\PrestacionesTipo;
use App\Models\PrestacionObsFase;
use Illuminate\Support\Facades\Log;

class PrestacionTipoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $prestacionObsFase = PrestacionObsFase::firstOrNew(['Id' => $data['Id']]);

        if (!$prestacionObsFase->exists) {

            $default = [
                'IdEntidad' => $data['IdEntidad'],
                'Comentario' => $data['Comentario'],
                'IdExamen' => $data['IdExamen'],
                'IdUsuario' => $data['IdUsuario'],
                'Fecha' => $data['Fecha'],
                'Rol' => $data['Rol'],
                'obsfases_id' => $data['obsfases_id']
            ];

            $prestacionObsFase->fill($default);
        }

        $prestacionObsFase->fill($data);
        $prestacionObsFase->save();
        Log::info("Prestacion Obs Fase {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        PrestacionObsFase::where('Id', $before['Id'])->delete();
        Log::info("Prestacion Obs Fase {$before['Id']} eliminada.");
    }

}