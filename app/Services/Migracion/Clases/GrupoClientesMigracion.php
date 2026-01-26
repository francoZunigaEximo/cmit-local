<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\GrupoClientes;
use Illuminate\Support\Facades\Log;

class GrupoClientesMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $grupoCliente = GrupoClientes::firstOrNew(['Id' => $data['Id']]);

        if (!$grupoCliente->exists) {

            $default = [
               'Nombre'=>$data['Nombre'],
                'Baja'=>$data['Baja']
            ];

            $grupoCliente->fill($default);
        }

        $grupoCliente->fill($data);
        $grupoCliente->save();
        Log::info("Grupo Clientes {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        GrupoClientes::where('Id', $before['Id'])->delete();
        Log::info("Grupo Clientes {$before['Id']} eliminado.");
    }

}