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
use Illuminate\Support\Facades\Log;

class PrestacionTipoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $prestacionTipo = PrestacionesTipo::firstOrNew(['Id' => $data['Id']]);

        if (!$prestacionTipo->exists) {

            $default = [
                'Nombre' => $data['Nombre'],
            ];

            $prestacionTipo->fill($default);
        }

        $prestacionTipo->fill($data);
        $prestacionTipo->save();
        Log::info("Prestacion Tipo {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        PrestacionesTipo::where('Id', $before['Id'])->delete();
        Log::info("Prestacion Tipo {$before['Id']} eliminada.");
    }

}