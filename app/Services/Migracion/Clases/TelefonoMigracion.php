<?php

namespace App\Services\Migration\Clases;

use App\Models\Telefono;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class TelefonoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $telefono = Telefono::firstOrCreate('Id', $data['Id']);

        if($telefono->exists) {
            $default = [
                'IdEntidad' => $data['IdEntidad'],
                'IdCliente' => $data['IdCliente'],
                'CodigoArea' => $data['CodigoArea'],
                'NumeroTelefono' => $data['NumeroTelefono'],
                'Observaciones' => $data['Observaciones'],
                'TipoEntidad' => $data['TipoEntidad'],
                'Id' => $data['Id'],
                'IdProfesional' => $data['IdProfesional']
            ];

            $telefono->fill($default);
        }

        $telefono->fill($data);
        $telefono->save();
        Log::info("Telefono {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Telefono::where('Id', $before['Id'])->delete();
        Log::info("Telefono {$before['Id']} eliminado.");
    }

}