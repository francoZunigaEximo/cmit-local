<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Mapa;
use Illuminate\Support\Facades\Log;

class MapaMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $mapa = Mapa::firstOrNew(['Id' => $data['Id']]);

        if (!$mapa->exists) {

            $default = [
                'Nro'=>$data['Nro'],
                'Fecha'=>$data['Fecha'],
                'IdART'=>$data['IdART'],
                'IdEmpresa'=>$data['IdEmpresa'],
                'Obs'=>$data['Obs'],
                'Inactivo'=>$data['Inactivo'],
                'Cpacientes'=>$data['Cpacientes'],
                'Cmapeados'=>$data['Cmapeados'],
                'FechaE'=>$data['FechaE'],
                'FechaAsignacion'=>$data['FechaAsignacion']
            ];

            $mapa->fill($default);
        }

        $mapa->fill($data);
        $mapa->save();
        Log::info("Mapa {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Mapa::where('Id', $before['Id'])->delete();
        Log::info("Mapa {$before['Id']} eliminado.");
    }

}