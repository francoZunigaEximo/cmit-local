<?php

namespace App\Enum;

enum RutasArchivos {

    const FACTURAS = 'facturas';
    const EXAMENESACUENTA = 'examenacuenta';

    public static function getRuta($ruta)
    {
        switch ($ruta) {
            case self::FACTURAS:
                return '/app/public/facturas/';
            case self::EXAMENESACUENTA:
                return '/app/public/examenescuenta/';
            default:
                return '/app/public/';
        }
    }
}