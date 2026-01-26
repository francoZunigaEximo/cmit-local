<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\HistorialPrestacion;
use App\Models\ItemPrestacion;
use App\Models\ItemPrestacionInfo;
use Illuminate\Support\Facades\Log;

class ItemPrestacionMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $itemPrestacionInfo = ItemPrestacionInfo::firstOrNew(['Id' => $data['Id']]);

        if (!$itemPrestacionInfo->exists) {

            $default = [
                'IdIP' => $data['IdIP'],
                'IdP' => $data['IdP'],
                'Obs' => $data['Obs'],
                'C1' => $data['C1'],
                'C2' => $data['C2']
            ];

            $itemPrestacionInfo->fill($default);
        }

        $itemPrestacionInfo->fill($data);
        $itemPrestacionInfo->save();
        Log::info("Item Prestacion Info {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        ItemPrestacionInfo::where('Id', $before['Id'])->delete();
        Log::info("Item Prestacion Info {$before['Id']} eliminado.");
    }

}