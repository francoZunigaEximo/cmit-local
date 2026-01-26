<?php

namespace App\Services\Migration\Clases;

use App\Models\ReporteFinneg;
use App\Models\Rol;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class RolMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $rol = Rol::firstOrCreate('Id', $data['Id']);

        if($rol->exists) {
            $default = [
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion']
            ];

            $rol->fill($default);
        }

        $rol->fill($data);
        $rol->save();
        Log::info("Rol {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Rol::where('Id', $before['Id'])->delete();
        Log::info("Rol {$before['Id']} eliminado.");
    }

}