<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Fichalaboral;
use Illuminate\Support\Facades\Log;

class FichaLaboralMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $fichaLaboral = Fichalaboral::firstOrNew(['Id' => $data['Id']]);

        if (!$fichaLaboral->exists) {

            $default = [
                'IdPaciente'=>$data['IdPaciente'],
                'IdEmpresa'=>$data['IdEmpresa'],
                'IdART'=>$data['IdART'],
                'Tareas'=>$data['Tareas'],
                'Puesto'=>$data['Puesto'],
                'Sector'=>$data['Sector'],
                'FechaIngreso'=>$data['FechaIngreso'],
                'FechaEgreso'=>$data['FechaEgreso'],
                'AntigPuesto'=>$data['AntigPuesto'],
                'Tipojornada'=>$data['Tipojornada'],
                'Jornada'=>$data['Jornada'],
                'TareasEmpAnterior'=>$data['TareasEmpAnterior'],
                'Observaciones'=>$data['Observaciones'],
                'TipoActividad'=>$data['TipoActividad'],
                'CCosto'=>$data['CCosto'],
                'Pago'=>$data['Pago'],
                'Estado'=>$data['Estado'],
                'TipoPrestacion'=>$data['TipoPrestacion'],
                'FechaPreocupacional'=>$data['FechaPreocupacional'],
                'FechaUltPeriod'=>$data['FechaUltPeriod'],
                'FechaExArt'=>$data['FechaExArt'],
                'SPago'=>$data['SPago'],
                'datos_facturacion_id'=>$data['datos_facturacion_id']
            ];

            $fichaLaboral->fill($default);
        }

        $fichaLaboral->fill($data);
        $fichaLaboral->save();
        Log::info("Ficha Laboral {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Fichalaboral::where('Id', $before['Id'])->delete();
        Log::info("Ficha Laboral {$before['Id']} eliminado.");
    }

}