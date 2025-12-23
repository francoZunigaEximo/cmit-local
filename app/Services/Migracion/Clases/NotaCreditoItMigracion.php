<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Mapa;
use App\Models\ModuloParametro;
use App\Models\NotaCredito;
use App\Models\NotaCreditoIt;
use Illuminate\Support\Facades\Log;

class NotaCreditoItMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $notaCreditoIt = NotaCreditoIt::firstOrNew(['Id' => $data['Id']]);

        if (!$notaCreditoIt->exists) {

            $default = [
                 'IdNC'=> $data['IdNC'],
                'IdIP'=>$data['IdIP'],
                'Estado'=>$data['Estado'],
                'FechaAnulado'=>$data['FechaAnulado'],
                'Baja'=>$data['Baja']
            ];

            $notaCreditoIt->fill($default);
        }

        $notaCreditoIt->fill($data);
        $notaCreditoIt->save();
        Log::info("NotaCreditoIt {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        NotaCreditoIt::where('Id', $before['Id'])->delete();
        Log::info("Nota Credito It {$before['Id']} eliminado.");
    }

}