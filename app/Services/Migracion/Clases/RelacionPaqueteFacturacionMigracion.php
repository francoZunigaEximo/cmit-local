<?php

namespace App\Services\Migration\Clases;

use App\Models\Proveedor;
use App\Models\RelacionGrupoCliente;
use App\Models\RelacionPaqueteFacturacion;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class RelacionPaqueteFacturacionMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $relacionPaqueteFacturacion = RelacionPaqueteFacturacion::firstOrCreate('Id', $data['Id']);

        if($relacionPaqueteFacturacion->exists) {
            $default = [
                'IdPaquete' => $data['IdPaquete'],
                'IdEstudio' => $data['IdEstudio'],
                'IdExamen' => $data['IdExamen'],
                'Baja' => $data['Baja']
            ];

            $relacionPaqueteFacturacion->fill($default);
        }

        $relacionPaqueteFacturacion->fill($data);
        $relacionPaqueteFacturacion->save();
        Log::info("Relacion Paquete Facturacion {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        RelacionPaqueteFacturacion::where('Id', $before['Id'])->delete();
        Log::info("Relacion Paquete Facturacion {$before['Id']} eliminado.");
    }

}