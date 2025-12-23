<?php

namespace App\Services\Migration\Clases;

use App\Models\ArchivoInformador;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ArchivoInformadorMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $archivoInformador = ArchivoInformador::firstOrCreate('Id', $data['Id']);

        if($archivoInformador->exists) {
            $default = [
                'IdEntidad'=>$data['IdEntidad'],
                'Descripcion'=>$data['Descripcion'],
                'Ruta'=>$data['Ruta'],
                'IdPrestacion'=>$data['IdPrestacion'],
                'PuntoCarga'=>$data['PuntoCarga']
            ];

            $archivoInformador->fill($default);
        }

        $archivoInformador->fill($data);
        $archivoInformador->save();
        Log::info("ArchivoInformador {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ArchivoInformador::where('Id', $before['Id'])->delete();
        Log::info("Archivo Informador {$before['Id']} eliminado.");
    }

}