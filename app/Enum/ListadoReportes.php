<?php

namespace App\Enum;

use App\Services\Reportes\Estudios\AudiometriaCmit;

class ListadoReportes
{
    public static function getReporte(int $id)
    {
        switch ($id) {
            case 1:
                return AudiometriaCmit::class;
        }
    }
}