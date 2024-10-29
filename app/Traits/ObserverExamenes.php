<?php

namespace App\Traits;

use App\Models\AliasExamen;
use App\Models\PaqueteEstudio;
use App\Models\Relpaqest;
use App\Models\Estudio;
use App\Models\ItemPrestacion;
use App\Models\Proveedor;
use App\Models\Reporte;

trait ObserverExamenes
{
    public function paqueteEstudio(int $id): mixed
    {
        return Relpaqest::where('IdPaquete', $id)->get();
    }

    public function getEstudios(): mixed
    {
        return Estudio::where('Id', '<>', 0)->get(['Id', 'Nombre']);  
    }

    public function getReportes(): mixed
    {
        return Reporte::where('Id', '<>', 0)->get(['Id', 'Nombre']);
    }

    public function getProveedor(): mixed
    {
        return Proveedor::where('Id', '<>', 0)->get(['Id', 'Nombre']);
    }

    public function auditarExamen(int $id)
    {
        return ItemPrestacion::where('IdExamen', $id)->get();

    }

    public function getAliasExamenes(): mixed
    {
        return AliasExamen::where('Id', '<>', 0)->get(['Id', 'Nombre']);
    }
}