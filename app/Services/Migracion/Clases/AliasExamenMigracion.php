<?php

namespace App\Services\Migration\Clases;

use App\Models\AliasExamen;
use App\Models\Telefono;
use App\Models\User;
use App\Models\UserSession;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class AliasExamenController implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $alias = AliasExamen::firstOrCreate('Id', $data['Id']);

        if($alias->exists) {
            $default = [
                'Nombre'=>$data['Nombre'],
                'Descripcion'=>$data['Descripcion']
            ];

            $alias->fill($default);
        }

        $alias->fill($data);
        $alias->save();
        Log::info("AliasExamen {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        AliasExamen::where('Id', $before['Id'])->delete();
        Log::info("Alias Examen {$before['Id']} eliminado.");
    }

}