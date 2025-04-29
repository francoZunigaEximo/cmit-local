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
use App\Services\Reportes\Estudios\RepsolIngreso;
use App\Services\Reportes\Estudios\EXAMENREPORTE11;
use App\Services\Reportes\Estudios\EXAMENREPORTE12;
use App\Services\Reportes\Estudios\EXAMENREPORTE13;
use App\Services\Reportes\Estudios\EXAMENREPORTE14;
use App\Services\Reportes\Estudios\EXAMENREPORTE15;
use App\Services\Reportes\Estudios\EXAMENREPORTE16;

class ListadoReportes
{
    
    public static function getReporte(int $id)
    {
        $examenes = [
            1 => AudiometriaCmit::class,
            2 => AudiometriaLiberty::class,
            3 => AudiometriaPrevMedica::class,
            4 => AudiometriaCarley::class,
            5 => EgresoPetreven::class,
            6 => EgresoPetreven::class,
            7 => EgresoRepsol::class,
            8 => HcPetreven::class,
            9 => IngresoPetreven::class,
            10 => RepsolIngreso::class,
            11 => EXAMENREPORTE11::class,
            12 => EXAMENREPORTE12::class,
            13 => EXAMENREPORTE13::class,
            14 => EXAMENREPORTE14::class,
            15 => EXAMENREPORTE15::class,
            16 => EXAMENREPORTE16::class
        ];

        return $examenes[$id];
    }
}