<?php

namespace App\Services\ItemsPrestaciones;

use App\Models\ExamenPrecioProveedor;

class Helper
{
    public function HoraAsegundos($hora){
        list($h,$m,$s) = explode(":",$hora);
        $segundos = ($h*3600)+($m*60) + $s;
        return $segundos;
    }

    public function SegundosAminutos($segundos){
        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos - ($horas * 3600)) / 60);
        return str_pad($horas,2, "0", STR_PAD_LEFT).':'.str_pad($minutos,2, "0", STR_PAD_LEFT);
    }
    
    public function honorarios(int $idExamen, int $idProveedor)
    {
        return ExamenPrecioProveedor::where('IdExamen', $idExamen)->where('IdProveedor', $idProveedor)->first(['Honorarios']);
    }

}