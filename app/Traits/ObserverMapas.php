<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Constanciase;

trait ObserverMapas
{
    
    public function constanciaseRemito($id, $obs)
    {
        Constanciase::create([
            'Id' => Constanciase::max('Id') +1,
            'NroC' => $id,
            'Fecha' => Carbon::now()->toDateTimeString(),
            'Obs' => $obs
        ]);
    } 
    

}