<?php

namespace App\Services\Migration\Clases;

use App\Models\ArchivoInformador;
use App\Models\ArchivoPrestacion;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ArchivoPrestacionMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $archivoPrestacion = ArchivoPrestacion::firstOrCreate('Id', $data['Id']);

        if($archivoPrestacion->exists) {
            $default = [
                'IdEntidad'=>$data['IdEntidad'],
                'Descripcion'=>$data['Descripcion'],
                'Ruta'=>$data['Ruta']
            ];

            $archivoPrestacion->fill($default);
        }

        $archivoPrestacion->fill($data);
        $archivoPrestacion->save();
        Log::info("Archivo Prestacion {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ArchivoPrestacion::where('Id', $before['Id'])->delete();
        Log::info("Archivo Prestacion {$before['Id']} eliminado.");
    }

}