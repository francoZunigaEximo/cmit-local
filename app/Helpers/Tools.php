<?php

namespace App\Helpers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Tools 
{
    public static function randomCode(int $longitud = 10)
    {
        return bin2hex(random_bytes($longitud/2));
    }

    public static function generarQR($tipo, $prestacionId, $examenId, $pacienteId, $out): mixed
    {
        // Tipo: A:efector, B:informador, C:evaluador (1 caracter)
        $prestacionId = str_pad($prestacionId, 9, "0", STR_PAD_LEFT);
        $examenId = str_pad($examenId, 5, "0", STR_PAD_LEFT);
        $pacienteId = str_pad($pacienteId, 7, "0", STR_PAD_LEFT);

        $path = storage_path('app/public/temp/qr_image.png');

        $code = strtoupper($tipo) . $prestacionId . $examenId . $pacienteId;

        QrCode::size(300)->format('png')->generate($code, $path);

        return $out === 'texto' ? $code : $path;
    }

}