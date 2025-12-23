<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Mapa;
use App\Models\ModuloParametro;
use App\Models\NotaCredito;
use Illuminate\Support\Facades\Log;

class NotaCreditoMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $notaCredito = NotaCredito::firstOrNew(['Id' => $data['Id']]);

        if (!$notaCredito->exists) {

            $default = [
                'Tipo'=>$data['Tipo'],
                'Sucursal'=>$data['Sucursal'],
                'Nro'=>$data['Nro'],
                'Fecha'=>$data['Fecha'],
                'IdEmpresa'=>$data['IdEmpresa'],
                'TipoCliente'=>$data['TipoCliente'],
                'IdFactura'=>$data['IdFactura'],
                'IdPrestacion'=>$data['IdPrestacion'],
                'TipoNC'=>$data['TipoNC'],
                'Obs'=>$data['Obs']
            ];

            $notaCredito->fill($default);
        }

        $notaCredito->fill($data);
        $notaCredito->save();
        Log::info("NotaCredito {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        NotaCredito::where('Id', $before['Id'])->delete();
        Log::info("Nota Credito {$before['Id']} eliminado.");
    }

}