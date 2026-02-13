<?php

namespace App\Services\Llamador;

use App\Models\AuditorAcciones;
use App\Models\AuditorTabla;

class Utils {

    const llamador = [
        'LLAMADOR',
        'LLAMADO',
        'ESTADO CERRADO',
        'ESTADO ABIERTO',
        'LIBERAR',
        'ASIGNAR EFECTOR',
        'DESASIGNAR EFECTOR'
    ];

    public function getIdAuditoria()
    {
        return AuditorTabla::where('Nombre', SELF::llamador[0])->first(['Id']);
    }

    public function getIdLlamado()
    {
        return AuditorAcciones::where('Nombre', SELF::llamador[1])->first(['Id']);
    }

    public function getIdCerrado()
    {
        return AuditorAcciones::where('Nombre', SELF::llamador[2])->first(['Id']);
    }

    public function getIdAbierto()
    {
        return AuditorAcciones::where('Nombre', SELF::llamador[3])->first(['Id']);
    }

    public function getIdLiberado()
    {
        return AuditorAcciones::where('Nombre', SELF::llamador[4])->first(['Id']);
    }

    public function getIdAsignarEfector()
    {
        return AuditorAcciones::where('Nombre', SELF::llamador[5])->first(['Id']);
    }

    public function getIdDesasignarEfector()
    {
        return AuditorAcciones::where('Nombre', SELF::llamador[6])->first(['Id']);
    }

}