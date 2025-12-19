<?php

namespace App\Services\Migration\Clases;

use App\Models\Proveedor;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ProveedoresMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $proveedor = Proveedor::firstOrCreate('Id', $data['Id']);

        if($proveedor->exists) {
            $default = [
                'Nombre' => $data['Nombre'],
                'Telefono' => $data['Telefono'],
                'Direccion' => $data['Direccion'],
                'IdLocalidad' => $data['IdLocalidad'],
                'Inactivo' => $data['Inactivo'],
                'Min' => $data['Min'],
                'PR' => $data['PR'],
                'Multi' => $data['Multi'],
                'MultiE' => $data['MultiE'],
                'InfAdj' => $data['InfAdj'],
                'Externo' => $data['Externo'],
                'Obs' => isset($data['Obs']) ? $data['Obs'] : ''
            ];

            $proveedor->fill($default);
        }

        $proveedor->fill($data);
        $proveedor->save();

        Log::info("Proveedor {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Proveedor::where('Id', $before['Id'])->delete();
        Log::info("Proveedor {$before['Id']} eliminado.");
    }

}