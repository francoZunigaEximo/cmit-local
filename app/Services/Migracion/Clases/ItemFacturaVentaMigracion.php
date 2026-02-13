<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\HistorialPrestacion;
use App\Models\ItemPrestacion;
use App\Models\ItemsFacturaVenta;
use Illuminate\Support\Facades\Log;

class ItemFacturaVentaMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $itemFacturaVenta = ItemsFacturaVenta::firstOrNew(['Id' => $data['Id']]);

        if (!$itemFacturaVenta->exists) {

            $default = [
                'IdFactura'=> $data['IdFactura'],
                'IdPrestacion'=> $data['IdPrestacion'],
                'Detalle'=> $data['Detalle'],
                'Anulado'=> $data['Anulado']
            ];

            $itemFacturaVenta->fill($default);
        }

        $itemFacturaVenta->fill($data);
        $itemFacturaVenta->save();
        Log::info("Item Factura Venta {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ItemsFacturaVenta::where('Id', $before['Id'])->delete();
        Log::info("Item Factura Venta {$before['Id']} eliminado.");
    }

}