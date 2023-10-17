<?php

namespace App\Traits;

use App\Models\ItemPrestacionInfo;

trait ObserverItemsPrestaciones
{

    public function createItemPrestacionInfo($id, $observacion)
    {

        ItemPrestacionInfo::create([
            'Id' => ItemPrestacionInfo::max('Id') + 1,
            'IdIP' => $id,
            'IdP' => 0,
            'Obs' => $observacion ?? '',
            'C1' => 0,
            'C2' => 0
        ]);
    }

    public function updateItemPrestacionInfo($id, $observacion)
    {

        $query = ItemPrestacionInfo::where('IdIP', $id)->first();
        $query->Obs = $observacion ?? '';
        $query->save();

    }
}