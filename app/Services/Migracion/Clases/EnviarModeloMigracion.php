<?php

namespace App\Services\Migration\Clases;

use App\Models\Autorizado;
use App\Models\Cliente;
use App\Models\Constanciase;
use App\Models\EnviarModelo;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class EnviarModeloMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $enviarModelo = EnviarModelo::firstOrCreate('Id', $data['Id']);

        if($enviarModelo->exists) {
            $default = [
                'Nombre'=> $data['Nombre'],
                'Asunto'=> $data['Asunto'],
                'Cuerpo'=> $data['Cuerpo']
            ];

            $enviarModelo->fill($default);
        }

        $enviarModelo->fill($data);
        $enviarModelo->save();
        Log::info("EnviarModelo {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        EnviarModelo::where('Id', $before['Id'])->delete();
        Log::info("Enviar Modelo {$before['Id']} eliminado.");
    }

}