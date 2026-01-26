<?php

namespace App\Services\Migration\Clases;

use App\Models\DatoPaciente;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class DatoPacienteMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $datosPaciente = DatoPaciente::firstOrCreate('Id', $data['Id']);

        if($datosPaciente->exists) {
            $default = [
                'IdPaciente'=>$data['IdPaciente'],
                'IdPrestacion'=>$data['IdPrestacion'],
                'Edad'=>$data['Edad'],
                'EstadoCivil'=>$data['EstadoCivil'],
                'ObsEC'=>$data['ObsEC'],
                'Direccion'=>$data['Direccion'],
                'IdLocalidad'=>$data['IdLocalidad'],
                'TipoActividad'=>$data['TipoActividad'],
                'Tareas'=>$data['Tareas'],
                'TareasEmpAnterior'=>$data['TareasEmpAnterior'],
                'Puesto'=>$data['Puesto'],
                'Sector'=>$data['Sector'],
                'FechaIngreso'=>$data['FechaIngreso'],
                'FechaEgreso'=>$data['FechaEgreso'],
                'AntigPuesto'=>$data['AntigPuesto'],
                'AntigEmpresa'=>$data['AntigEmpresa'],
                'TipoJornada'=>$data['TipoJornada'],
                'Jornada'=>$data['Jornada'],
                'ObsJornada'=>$data['ObsJornada'],
                'CCosto'=>$data['CCosto']
            ];

            $datosPaciente->fill($default);
        }

        $datosPaciente->fill($data);
        $datosPaciente->save();
        Log::info("DatosPaciente {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        DatoPaciente::where('Id', $before['Id'])->delete();
        Log::info("DatosPaciente {$before['Id']} eliminado.");
    }

}