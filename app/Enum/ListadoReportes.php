<?php

namespace App\Enum;

use App\Services\Reportes\Estudios\AudiometriaCmit;
use App\Services\Reportes\Estudios\AudiometriaLiberty;
use App\Services\Reportes\Estudios\AudiometriaPrevMedica;
use App\Services\Reportes\Estudios\AudiometriaCarley;
use App\Services\Reportes\Estudios\EgresoPetreven;
use App\Services\Reportes\Estudios\EgresoRepsol;
use App\Services\Reportes\Estudios\EXAMENREPORTE100;
use App\Services\Reportes\Estudios\EXAMENREPORTE101;
use App\Services\Reportes\Estudios\EXAMENREPORTE103;
use App\Services\Reportes\Estudios\EXAMENREPORTE104;
use App\Services\Reportes\Estudios\EXAMENREPORTE105;
use App\Services\Reportes\Estudios\EXAMENREPORTE106;
use App\Services\Reportes\Estudios\EXAMENREPORTE107;
use App\Services\Reportes\Estudios\EXAMENREPORTE108;
use App\Services\Reportes\Estudios\EXAMENREPORTE109;
use App\Services\Reportes\Estudios\HcPetreven;
use App\Services\Reportes\Estudios\IngresoPetreven;
use App\Services\Reportes\Estudios\RepsolIngreso;
use App\Services\Reportes\Estudios\EXAMENREPORTE11;
use App\Services\Reportes\Estudios\EXAMENREPORTE111;
use App\Services\Reportes\Estudios\EXAMENREPORTE112;
use App\Services\Reportes\Estudios\EXAMENREPORTE113;
use App\Services\Reportes\Estudios\EXAMENREPORTE114;
use App\Services\Reportes\Estudios\EXAMENREPORTE115;
use App\Services\Reportes\Estudios\EXAMENREPORTE116;
use App\Services\Reportes\Estudios\EXAMENREPORTE117;
use App\Services\Reportes\Estudios\EXAMENREPORTE118;
use App\Services\Reportes\Estudios\EXAMENREPORTE119;
use App\Services\Reportes\Estudios\EXAMENREPORTE12;
use App\Services\Reportes\Estudios\EXAMENREPORTE120;
use App\Services\Reportes\Estudios\EXAMENREPORTE121;
use App\Services\Reportes\Estudios\EXAMENREPORTE122;
use App\Services\Reportes\Estudios\EXAMENREPORTE123;
use App\Services\Reportes\Estudios\EXAMENREPORTE124;
use App\Services\Reportes\Estudios\EXAMENREPORTE125;
use App\Services\Reportes\Estudios\EXAMENREPORTE126;
use App\Services\Reportes\Estudios\EXAMENREPORTE127;
use App\Services\Reportes\Estudios\EXAMENREPORTE128;
use App\Services\Reportes\Estudios\EXAMENREPORTE129;
use App\Services\Reportes\Estudios\EXAMENREPORTE13;
use App\Services\Reportes\Estudios\EXAMENREPORTE130;
use App\Services\Reportes\Estudios\EXAMENREPORTE131;
use App\Services\Reportes\Estudios\EXAMENREPORTE132;
use App\Services\Reportes\Estudios\EXAMENREPORTE133;
use App\Services\Reportes\Estudios\EXAMENREPORTE134;
use App\Services\Reportes\Estudios\EXAMENREPORTE135;
use App\Services\Reportes\Estudios\EXAMENREPORTE136;
use App\Services\Reportes\Estudios\EXAMENREPORTE137;
use App\Services\Reportes\Estudios\EXAMENREPORTE138;
use App\Services\Reportes\Estudios\EXAMENREPORTE139;
use App\Services\Reportes\Estudios\EXAMENREPORTE14;
use App\Services\Reportes\Estudios\EXAMENREPORTE140;
use App\Services\Reportes\Estudios\EXAMENREPORTE141;
use App\Services\Reportes\Estudios\EXAMENREPORTE142;
use App\Services\Reportes\Estudios\EXAMENREPORTE143;
use App\Services\Reportes\Estudios\EXAMENREPORTE144;
use App\Services\Reportes\Estudios\EXAMENREPORTE145;
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
use App\Services\Reportes\Estudios\EXAMENREPORTE58;
use App\Services\Reportes\Estudios\EXAMENREPORTE59;
use App\Services\Reportes\Estudios\EXAMENREPORTE60;
use App\Services\Reportes\Estudios\EXAMENREPORTE61;
use App\Services\Reportes\Estudios\EXAMENREPORTE62;
use App\Services\Reportes\Estudios\EXAMENREPORTE63;
use App\Services\Reportes\Estudios\EXAMENREPORTE64;
use App\Services\Reportes\Estudios\EXAMENREPORTE65;
use App\Services\Reportes\Estudios\EXAMENREPORTE66;
use App\Services\Reportes\Estudios\EXAMENREPORTE67;
use App\Services\Reportes\Estudios\EXAMENREPORTE68;
use App\Services\Reportes\Estudios\EXAMENREPORTE69;
use App\Services\Reportes\Estudios\EXAMENREPORTE70;
use App\Services\Reportes\Estudios\EXAMENREPORTE71;
use App\Services\Reportes\Estudios\EXAMENREPORTE72;
use App\Services\Reportes\Estudios\EXAMENREPORTE73;
use App\Services\Reportes\Estudios\EXAMENREPORTE74;
use App\Services\Reportes\Estudios\EXAMENREPORTE75;
use App\Services\Reportes\Estudios\EXAMENREPORTE76;
use App\Services\Reportes\Estudios\EXAMENREPORTE77;
use App\Services\Reportes\Estudios\EXAMENREPORTE78;
use App\Services\Reportes\Estudios\EXAMENREPORTE79;
use App\Services\Reportes\Estudios\EXAMENREPORTE80;
use App\Services\Reportes\Estudios\EXAMENREPORTE81;
use App\Services\Reportes\Estudios\EXAMENREPORTE82;
use App\Services\Reportes\Estudios\EXAMENREPORTE83;
use App\Services\Reportes\Estudios\EXAMENREPORTE84;
use App\Services\Reportes\Estudios\EXAMENREPORTE85;
use App\Services\Reportes\Estudios\EXAMENREPORTE86;
use App\Services\Reportes\Estudios\EXAMENREPORTE87;
use App\Services\Reportes\Estudios\EXAMENREPORTE88;
use App\Services\Reportes\Estudios\EXAMENREPORTE89;
use App\Services\Reportes\Estudios\EXAMENREPORTE90;
use App\Services\Reportes\Estudios\EXAMENREPORTE91;
use App\Services\Reportes\Estudios\EXAMENREPORTE92;
use App\Services\Reportes\Estudios\EXAMENREPORTE93;
use App\Services\Reportes\Estudios\EXAMENREPORTE94;
use App\Services\Reportes\Estudios\EXAMENREPORTE95;
use App\Services\Reportes\Estudios\EXAMENREPORTE96;
use App\Services\Reportes\Estudios\EXAMENREPORTE97;
use App\Services\Reportes\Estudios\EXAMENREPORTE98;
use App\Services\Reportes\Estudios\EXAMENREPORTE99;

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
            57 => EXAMENREPORTE57::class,
            58 => EXAMENREPORTE58::class,
            59 => EXAMENREPORTE59::class,
            60 => EXAMENREPORTE60::class,
            61 => EXAMENREPORTE61::class,
            62 => EXAMENREPORTE62::class,
            63 => EXAMENREPORTE63::class,
            64 => EXAMENREPORTE64::class,
            65 => EXAMENREPORTE65::class,
            66 => EXAMENREPORTE66::class,
            67 => EXAMENREPORTE67::class,
            68 => EXAMENREPORTE68::class,
            69 => EXAMENREPORTE69::class,
            70 => EXAMENREPORTE70::class,
            71 => EXAMENREPORTE71::class,
            72 => EXAMENREPORTE72::class,
            73 => EXAMENREPORTE73::class,
            74 => EXAMENREPORTE74::class,
            75 => EXAMENREPORTE75::class,
            76 => EXAMENREPORTE76::class,
            77 => EXAMENREPORTE77::class,
            78 => EXAMENREPORTE78::class,
            79 => EXAMENREPORTE79::class,
            80 => EXAMENREPORTE80::class,
            81 => EXAMENREPORTE81::class,
            82 => EXAMENREPORTE82::class,
            83 => EXAMENREPORTE83::class,
            84 => EXAMENREPORTE84::class,
            85 => EXAMENREPORTE85::class,
            86 => EXAMENREPORTE86::class,
            87 => EXAMENREPORTE87::class,
            88 => EXAMENREPORTE88::class,
            89 => EXAMENREPORTE89::class,
            90 => EXAMENREPORTE90::class,
            91 => EXAMENREPORTE91::class,
            92 => EXAMENREPORTE92::class,
            93 => EXAMENREPORTE93::class,
            94 => EXAMENREPORTE94::class,
            95 => EXAMENREPORTE95::class,
            96 => EXAMENREPORTE96::class,
            97 => EXAMENREPORTE97::class,
            98 => EXAMENREPORTE98::class,
            99 => EXAMENREPORTE99::class,
            100 => EXAMENREPORTE100::class,
            101 => EXAMENREPORTE101::class,
            103 => EXAMENREPORTE103::class,
            104 => EXAMENREPORTE104::class,
            105 => EXAMENREPORTE105::class,
            106 => EXAMENREPORTE106::class,
            107 => EXAMENREPORTE107::class,
            108 => EXAMENREPORTE108::class,
            109 => EXAMENREPORTE109::class,
            111 => EXAMENREPORTE111::class,
            112 => EXAMENREPORTE112::class,
            113 => EXAMENREPORTE113::class,
            114 => EXAMENREPORTE114::class,
            115 => EXAMENREPORTE115::class,
            116 => EXAMENREPORTE116::class,
            117 => EXAMENREPORTE117::class,
            118 => EXAMENREPORTE118::class,
            119 => EXAMENREPORTE119::class,
            120 => EXAMENREPORTE120::class,
            121 => EXAMENREPORTE121::class,
            122 => EXAMENREPORTE122::class,
            123 => EXAMENREPORTE123::class,
            124 => EXAMENREPORTE124::class,
            125 => EXAMENREPORTE125::class,
            126 => EXAMENREPORTE126::class,
            127 => EXAMENREPORTE127::class,
            128 => EXAMENREPORTE128::class,
            129 => EXAMENREPORTE129::class,
            130 => EXAMENREPORTE130::class,
            131 => EXAMENREPORTE131::class,
            132 => EXAMENREPORTE132::class,
            133 => EXAMENREPORTE133::class,
            134 => EXAMENREPORTE134::class,
            135 => EXAMENREPORTE135::class,
            136 => EXAMENREPORTE136::class,
            137 => EXAMENREPORTE137::class,
            138 => EXAMENREPORTE138::class,
            139 => EXAMENREPORTE139::class,
            140 => EXAMENREPORTE140::class,
            141 => EXAMENREPORTE141::class,
            142 => EXAMENREPORTE142::class,
            143 => EXAMENREPORTE143::class,
            144 => EXAMENREPORTE144::class,
            145 => EXAMENREPORTE145::class,
        ];

        return $examenes[$id];
    }
}