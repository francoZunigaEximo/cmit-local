<?php

namespace App\Services\Migration\Clases;

use App\Models\Autorizado;
use App\Models\Cliente;
use App\Models\Constanciase;
use App\Models\ConstanciaseIt;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ConstanciaseItMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $constanciaseIt = ConstanciaseIt::firstOrCreate('Id', $data['Id']);

        if($constanciaseIt->exists) {
            $default = [
                'IdC'=>$data['IdC'],
                'IdP'=>$data['IdP']
            ];

            $constanciaseIt->fill($default);
        }

        $constanciaseIt->fill($data);
        $constanciaseIt->save();
        Log::info("ConstanciaseIt {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ConstanciaseIt::where('Id', $before['Id'])->delete();
        Log::info("ConstanciaseIt {$before['Id']} eliminado.");
    }

}