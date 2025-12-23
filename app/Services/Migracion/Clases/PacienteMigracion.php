<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Paciente;
use Illuminate\Support\Facades\Log;

class PacienteMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $paciente = Paciente::firstOrNew(['Id' => $data['Id']]);

        if (!$paciente->exists) {

            $default = [
                'TipoIdentificacion' => $data['TipoIdentificacion'],
                'Identificacion' => $data['Identificacion'],
                'TipoDocumento' => $data['TipoDocumento'],
                'Documento' => $data['Documento'],
                'Nacionalidad' => $data['Nacionalidad'],
                'Sexo' => $data['Sexo'],
                'Nombre' => $data['Nombre'],
                'Apellido' => $data['Apellido'],
                'FechaNacimiento' => $data['FechaNacimiento'],
                'LugarNacimiento' => $data['LugarNacimiento'],
                'EstadoCivil' => $data['EstadoCivil'],
                'ObsEstadoCivil' => $data['ObsEstadoCivil'],
                'Direccion' => $data['Direccion'],
                'CP' => $data['CP'],
                'EMail' => $data['EMail'],
                'ObsEMail' => $data['ObsEMail'],
                'Foto' => $data['Foto'],
                'Antecedentes' => $data['Antecedentes'],
                'Observaciones' => $data['Observaciones'],
                'Estado' => $data['Estado'],
                'Provincia' => $data['Provincia'],
                'IdLocalidad' => $data['IdLocalidad'],
                'ObsEMail' => $data['ObsEMail'],
                'Id' => $data['Id'],
                'ciudad' => $data['ciudad'],
                'provincia2' => $data['provincia2']
            ];

            $paciente->fill($default);
        }

        $paciente->fill($data);
        $paciente->save();
        Log::info("Paciente {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Paciente::where('Id', $before['Id'])->delete();
        Log::info("Paciente {$before['Id']} eliminado.");
    }

}