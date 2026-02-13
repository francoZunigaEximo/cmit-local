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
use Illuminate\Support\Facades\Log;

class PrestacionAtributoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $prestacionComentario = PrestacionComentario::firstOrNew(['Id' => $data['Id']]);

        if (!$prestacionComentario->exists) {
            
            $default = [
                'IdP' => $data['IdP'],
                'Obs' => $data['Obs'],
            ];

            $prestacionComentario->fill($default);
        }

        $prestacionComentario->fill($data);
        $prestacionComentario->save();
        Log::info("Prestacion Comentario {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        PrestacionComentario::where('Id', $before['Id'])->delete();
        Log::info("Prestacion Comentario {$before['Id']} eliminada.");
    }

}