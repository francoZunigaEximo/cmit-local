<?php

namespace App\Enum;

enum TituloReporte 
{
    const FACTURAS = 'factura';
    const EXAMENESACUENTA = 'examenacuenta';

    public static function getTitulo($titulo)
    {
        switch ($titulo) {
            case self::FACTURAS:
                return 'DETALLE DE FACTURA';
            case self::EXAMENESACUENTA:
                return 'DETALLE DE EXAMEN DE CUENTA';
            default:
                return 'DETALLE';
        }
    }
}