<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ItemPrestacion;
use App\Models\ItemsFacturaCompra2;
use Illuminate\Support\Facades\Log;

class ItemsFacturaCompra2Migracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $itemFacturaCompra2 = ItemsFacturaCompra2::firstOrNew(['Id' => $data['Id']]);

        if (!$itemFacturaCompra2->exists) {

            $default = [
               'IdFactura'=> $data['IdFactura'],
                'IdItemPrestacion' =>$data['IdItemPrestacion']      
            ];

            $itemFacturaCompra2->fill($default);
        }

        $itemFacturaCompra2->fill($data);
        $itemFacturaCompra2->save();
        Log::info("Item Factura Compra 2 {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ItemsFacturaCompra2::where('Id', $before['Id'])->delete();
        Log::info("Item Factura Compra 2 {$before['Id']} eliminado.");
    }

}