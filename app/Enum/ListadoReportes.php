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
use App\Services\Reportes\Estudios\EXAMENREPORTE17;
use App\Services\Reportes\Estudios\EXAMENREPORTE18;
use App\Services\Reportes\Estudios\EXAMENREPORTE19;
use App\Services\Reportes\Estudios\EXAMENREPORTE20;
use App\Services\Reportes\Estudios\EXAMENREPORTE21;
use App\Services\Reportes\Estudios\EXAMENREPORTE22;
use App\Services\Reportes\Estudios\EXAMENREPORTE23;
use App\Services\Reportes\Estudios\EXAMENREPORTE24;
use App\Services\Reportes\Estudios\EXAMENREPORTE25;
use App\Services\Reportes\Estudios\EXAMENREPORTE26;
use App\Services\Reportes\Estudios\EXAMENREPORTE27;
use App\Services\Reportes\Estudios\EXAMENREPORTE28;

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
            16 => EXAMENREPORTE16::class,
            17 => EXAMENREPORTE17::class,
            18 => EXAMENREPORTE18::class,
            19 => EXAMENREPORTE19::class,
            20 => EXAMENREPORTE20::class,
            21 => EXAMENREPORTE21::class,
            22 => EXAMENREPORTE22::class,
            23 => EXAMENREPORTE23::class,
            24 => EXAMENREPORTE24::class,
            25 => EXAMENREPORTE25::class,
            26 => EXAMENREPORTE26::class,
            27 => EXAMENREPORTE27::class,
            28 => EXAMENREPORTE28::class,
        ];

        return $examenes[$id];
    }
}