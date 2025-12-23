<?php

namespace App\Services\Migration\Clases;

use App\Models\ExamenCuenta;
use App\Models\ExamenPrecioProveedor;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ExamenPrecioProveedorMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $examen = ExamenPrecioProveedor::firstOrCreate('Id', $data['Id']);

        if($examen->exists) {
            $default = [
                'IdEstudio'=>$data['IdEstudio'],
                'IdExamen'=>$data['IdExamen'],
                'IdProveedor'=>$data['IdProveedor'],
                'Honorarios'=>$data['Honorarios']
            ];

            $examen->fill($default);
        }

        $examen->fill($data);
        $examen->save();
        Log::info("Examen Precio Proveedor {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ExamenPrecioProveedor::where('Id', $before['Id'])->delete();
        Log::info("Examen Precio Proveedor {$before['Id']} eliminado.");
    }

}