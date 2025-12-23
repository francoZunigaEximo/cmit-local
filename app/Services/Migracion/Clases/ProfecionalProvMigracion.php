<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ProfesionalProv;
use Illuminate\Support\Facades\Log;

class ProfecionalProvMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $profecionalProv = ProfesionalProv::firstOrNew(['Id' => $data['Id']]);

        if (!$profecionalProv->exists) {

            $default = [
                'IdProf'=>$data['IdProf'],
                'IdProv'=>$data['IdProv'],
                'IdRol'=>$data['IdRol'],
                'Tipo'=>$data['Tipo']
            ];

            $profecionalProv->fill($default);
        }

        $profecionalProv->fill($data);
        $profecionalProv->save();
        Log::info("Profesional Prov {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ProfesionalProv::where('Id', $before['Id'])->delete();
        Log::info("Profesional Prov {$before['Id']} eliminado.");
    }

}