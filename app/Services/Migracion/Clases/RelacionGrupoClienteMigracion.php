<?php

namespace App\Services\Migration\Clases;

use App\Models\Proveedor;
use App\Models\RelacionGrupoCliente;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class RelacionGrupoClienteMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $relacionGrupoCliente = RelacionGrupoCliente::firstOrCreate('Id', $data['Id']);

        if($relacionGrupoCliente->exists) {
            $default = [
                'IdGrupo' => $data['IdGrupo'],
                'IdCliente' => $data['IdCliente'],
                'Baja' => $data['Baja']
            ];

            $relacionGrupoCliente->fill($default);
        }

        $relacionGrupoCliente->fill($data);
        $relacionGrupoCliente->save();
        Log::info("Relacion Grupo Cliente {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        RelacionGrupoCliente::where('Id', $before['Id'])->delete();
        Log::info("Relacion Grupo Cliente {$before['Id']} eliminado.");
    }

}