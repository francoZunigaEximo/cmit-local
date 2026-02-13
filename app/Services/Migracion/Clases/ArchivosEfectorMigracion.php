<?php

namespace App\Services\Migration\Clases;

use App\Models\ArchivoEfector;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ArchivosEfectorMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $archivo = ArchivoEfector::firstOrNew('Id', $data['Id']);

        if($archivo->exists) {
            $default =  [
                'IdEntidad' => $data['IdEntidad'],
                'Descripcion' => $data['Descripcion'],
                'Ruta' => $data['Ruta'],
                'IdPrestacion' => $data['IdPrestacion'],
                'Tipo' => $data['Tipo'],
                'PuntoCarga' => isset($data['PuntoCarga']) ? $data['PuntoCarga'] : ''
            ];

            $archivo->fill($default);
        }

        $archivo->fill($data);
        $archivo->save();

        Log::info("Archivo efector {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ArchivoEfector::where('Id', $before['Id'])->delete();
        Log::info("Archivo efector {$before['Id']} eliminado.");
    }

}