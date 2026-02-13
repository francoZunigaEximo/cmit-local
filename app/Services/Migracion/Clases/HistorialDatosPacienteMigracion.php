<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\GrupoClientes;
use App\Models\HistorialDatoPaciente;
use Illuminate\Support\Facades\Log;

class HistorialDatosPacienteMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $historialDatosPaciente = HistorialDatoPaciente::firstOrNew(['Id' => $data['Id']]);

        if (!$historialDatosPaciente->exists) {

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

            $historialDatosPaciente->fill($default);
        }

        $historialDatosPaciente->fill($data);
        $historialDatosPaciente->save();
        Log::info("Historial Datos Paciente {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        HistorialDatoPaciente::where('Id', $before['Id'])->delete();
        Log::info("Historial Datos Paciente {$before['Id']} eliminado.");
    }

}