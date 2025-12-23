<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ParametroReporte;
use App\Models\Permiso;
use App\Models\Personal;
use Illuminate\Support\Facades\Log;

class PermisoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $personal = Personal::firstOrNew(['Id' => $data['Id']]);

        if (!$personal->exists) {

            $default = [
                'TipoIdentificacion' => $data['TipoIdentificacion'],
                'Apellido' => $data['Apellido'],
                'Nombre' => $data['Nombre'],
                'TipoDocumento' => $data['TipoDocumento'],
                'Documento' => $data['Documento'],
                'Identificacion' => $data['Identificacion'],
                'Telefono' => $data['Telefono'],
                'FechaNacimiento' => $data['FechaNacimiento'],
                'Provincia' => $data['Provincia'],
                'IdLocalidad' => $data['IdLocalidad'], 
                'CP' => $data['CP'],
                'Direccion' => $data['Direccion']
            ];

            $personal->fill($default);
        }

        $personal->fill($data);
        $personal->save();
        Log::info("Personal {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Personal::where('Id', $before['Id'])->delete();
        Log::info("Personal {$before['Id']} eliminado.");
    }

}