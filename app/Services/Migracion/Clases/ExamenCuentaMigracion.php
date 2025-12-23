<?php

namespace App\Services\Migration\Clases;

use App\Models\ExamenCuenta;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ExamenCuentaMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $examen = ExamenCuenta::firstOrCreate('Id', $data['Id']);

        if($examen->exists) {
            $default = [
                'IdEmpresa'=>$data['IdEmpresa'],
                'Fecha'=>$data['Fecha'],
                'Tipo'=>$data['Tipo'],
                'Suc'=>$data['Suc'],
                'Nro'=>$data['Nro'],
                'Obs'=>$data['Obs'],
                'Pagado'=>$data['Pagado'],
                'FechaP'=>$data['FechaP']
            ];

            $examen->fill($default);
        }

        $examen->fill($data);
        $examen->save();
        Log::info("ExamenCuenta {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ExamenCuenta::where('Id', $before['Id'])->delete();
        Log::info("ExamenCuenta {$before['Id']} eliminado.");
    }

}