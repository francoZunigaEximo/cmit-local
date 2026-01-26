<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ItemPrestacionInfo;
use App\Models\ItemsFacturaCompra;
use Illuminate\Support\Facades\Log;

class ItemsFacturaCompraMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $itemFacturaCompra = ItemsFacturaCompra::firstOrNew(['Id' => $data['Id']]);

        if (!$itemFacturaCompra->exists) {

            $default = [
                'IdFactura'=>$data['IdFactura'],
                'IdItemPrestacion'=>$data['IdItemPrestacion']
            ];

            $itemFacturaCompra->fill($default);
        }

        $itemFacturaCompra->fill($data);
        $itemFacturaCompra->save();
        Log::info("Item Factura Compra {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ItemsFacturaCompra::where('Id', $before['Id'])->delete();
        Log::info("Item Factura Compra {$before['Id']} eliminado.");
    }

}