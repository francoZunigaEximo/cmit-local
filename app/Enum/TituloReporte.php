<?php

namespace App\Enum;

class TituloReporte 
{
    public static $FACTURAS = 'factura';
    public static $EXAMENESACUENTA = 'examenacuenta';

    public static function getTitulo($titulo)
    {
        switch ($titulo) {
            case self::$FACTURAS:
                return 'DETALLE DE FACTURA';
            case self::$EXAMENESACUENTA:
                return 'DETALLE DE EXAMEN DE CUENTA';
            default:
                return 'DETALLE';
        }
    }
}