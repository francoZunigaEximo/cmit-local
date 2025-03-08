<?php

namespace App\Enum;

use App\Services\Reportes\Estudios\AudiometriaCmit;
use App\Services\Reportes\Estudios\AudiometriaLiberty;
use App\Services\Reportes\Estudios\AudiometriaPrevMedica;
use App\Services\Reportes\Estudios\AudiometriaCarley;
use App\Services\Reportes\Estudios\EgresoPetreven;
use App\Services\Reportes\Estudios\EgresoRepsol;
use App\Services\Reportes\Estudios\HcPetreven;
use App\Services\Reportes\Estudios\IngresoPetreven;

class ListadoReportes
{
    public static function getReporte(int $id)
    {
        switch ($id) {
            case 1:
                return AudiometriaCmit::class;
            case 2:
                return AudiometriaLiberty::class;
            case 3:
                return AudiometriaPrevMedica::class;
            case 4:
                return AudiometriaCarley::class;
            case 6:
                return EgresoPetreven::class;
            case 7:
                return EgresoRepsol::class;
            case 8:
                return HcPetreven::class;
            case 9:
                return IngresoPetreven::class;
        }
    }
}