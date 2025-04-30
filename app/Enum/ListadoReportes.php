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
use App\Services\Reportes\Estudios\EXAMENREPORTE29;
use App\Services\Reportes\Estudios\EXAMENREPORTE30;
use App\Services\Reportes\Estudios\EXAMENREPORTE31;
use App\Services\Reportes\Estudios\EXAMENREPORTE32;
use App\Services\Reportes\Estudios\EXAMENREPORTE33;
use App\Services\Reportes\Estudios\EXAMENREPORTE34;
use App\Services\Reportes\Estudios\EXAMENREPORTE35;
use App\Services\Reportes\Estudios\EXAMENREPORTE36;
use App\Services\Reportes\Estudios\EXAMENREPORTE37;
use App\Services\Reportes\Estudios\EXAMENREPORTE38;
use App\Services\Reportes\Estudios\EXAMENREPORTE39;
use App\Services\Reportes\Estudios\EXAMENREPORTE40;
use App\Services\Reportes\Estudios\EXAMENREPORTE41;
use App\Services\Reportes\Estudios\EXAMENREPORTE42;
use App\Services\Reportes\Estudios\EXAMENREPORTE43;
use App\Services\Reportes\Estudios\EXAMENREPORTE44;
use App\Services\Reportes\Estudios\EXAMENREPORTE45;
use App\Services\Reportes\Estudios\EXAMENREPORTE46;
use App\Services\Reportes\Estudios\EXAMENREPORTE47;
use App\Services\Reportes\Estudios\EXAMENREPORTE48;
use App\Services\Reportes\Estudios\EXAMENREPORTE49;
use App\Services\Reportes\Estudios\EXAMENREPORTE50;
use App\Services\Reportes\Estudios\EXAMENREPORTE51;
use App\Services\Reportes\Estudios\EXAMENREPORTE52;
use App\Services\Reportes\Estudios\EXAMENREPORTE53;
use App\Services\Reportes\Estudios\EXAMENREPORTE54;
use App\Services\Reportes\Estudios\EXAMENREPORTE55;
use App\Services\Reportes\Estudios\EXAMENREPORTE56;
use App\Services\Reportes\Estudios\EXAMENREPORTE57;

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
            29 => EXAMENREPORTE29::class,
            30 => EXAMENREPORTE30::class,
            31 => EXAMENREPORTE31::class,
            32 => EXAMENREPORTE32::class,
            33 => EXAMENREPORTE33::class,
            34 => EXAMENREPORTE34::class,
            35 => EXAMENREPORTE35::class,
            36 => EXAMENREPORTE36::class,
            37 => EXAMENREPORTE37::class,
            38 => EXAMENREPORTE38::class,
            39 => EXAMENREPORTE39::class,
            40 => EXAMENREPORTE40::class,
            41 => EXAMENREPORTE41::class,
            42 => EXAMENREPORTE42::class,
            43 => EXAMENREPORTE43::class,
            44 => EXAMENREPORTE44::class,
            45 => EXAMENREPORTE45::class,
            46 => EXAMENREPORTE46::class,
            47 => EXAMENREPORTE47::class,
            48 => EXAMENREPORTE48::class,
            49 => EXAMENREPORTE49::class,
            50 => EXAMENREPORTE50::class,
            51 => EXAMENREPORTE51::class,
            52 => EXAMENREPORTE52::class,
            53 => EXAMENREPORTE53::class,
            54 => EXAMENREPORTE54::class,
            55 => EXAMENREPORTE55::class,
            56 => EXAMENREPORTE56::class,
            57 => EXAMENREPORTE57::class
        ];

        return $examenes[$id];
    }
}