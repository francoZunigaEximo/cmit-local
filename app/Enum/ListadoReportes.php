<?php

namespace App\Enum;

use App\Services\Reportes\Estudios\AudiometriaCmit;
use App\Services\Reportes\Estudios\AudiometriaLiberty;
use App\Services\Reportes\Estudios\AudiometriaPrevMedica;
use App\Services\Reportes\Estudios\AudiometriaCarley;
use App\Services\Reportes\Estudios\EgresoPetreven;

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

        }
    }
}