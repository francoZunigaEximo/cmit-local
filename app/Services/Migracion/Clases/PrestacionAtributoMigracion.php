<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ParametroReporte;
use App\Models\Permiso;
use App\Models\Personal;
use App\Models\PrecioPorCodigo;
use App\Models\Prestacion;
use App\Models\PrestacionAtributo;
use Illuminate\Support\Facades\Log;

class PrestacionAtributoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $prestacionAtributo = PrestacionAtributo::firstOrNew(['Id' => $data['Id']]);

        if (!$prestacionAtributo->exists) {

            $default = [
                'IdPadre' => $data['IdPadre'],
                'SinEval' => $data['SinEval']
            ];

            $prestacionAtributo->fill($default);
        }

        $prestacionAtributo->fill($data);
        $prestacionAtributo->save();
        Log::info("Prestacion Atributo {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        PrestacionAtributo::where('Id', $before['Id'])->delete();
        Log::info("Prestacion Atributo {$before['Id']} eliminada.");
    }

}