<?php

namespace App\Traits;

use App\Models\ExamenCuentaIt;

trait ObserverExamenesCuenta
{
    public function examenProvisorio(int $id): void
    {
        ExamenCuentaIt::create([
            'Id' => ExamenCuentaIt::max('Id') + 1,
            'IdPago' => $id,
            'IdExamen' => 0,
            'IdPrestacion' => 0,
            'Obs' => 'provisorio',
            'Obs2' => 'provisorio',
            'Precarga' => 0
        ]);
    }
}