<?php

namespace App\Helpers;

use App\Models\Paciente;
use App\Models\Prestacion;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Tools 
{
    public static function randomCode(int $longitud = 10)
    {
        return bin2hex(random_bytes($longitud/2));
    }

    public static function generarQR($tipo, $prestacionId, $examenId, $pacienteId, $out, $pdf = null): mixed
    {
        $paciente = Paciente::find($pacienteId);
        $prestacion = Prestacion::find($prestacionId);

        // Tipo: A:efector, B:informador, C:evaluador (1 caracter)
        $prestacionId = str_pad($prestacionId, 9, "0", STR_PAD_LEFT);
        $examenId = str_pad($examenId, 5, "0", STR_PAD_LEFT);
        $pacienteId = str_pad($pacienteId, 7, "0", STR_PAD_LEFT);

        $path = storage_path('app/public/temp/qr_image.png');

        $code = strtoupper($tipo) . $prestacionId . $examenId . $pacienteId;

        QrCode::size(300)->format('png')->generate($code, $path);
        if($pdf){
            $pdf->SetXY(100,5);$pdf->Cell(85,3,$paciente->Apellido.' '.$paciente->Nombre,0,0,'R');
            $pdf->SetXY(100,10);$pdf->Cell(85,3,$paciente->Documento,0,0,'R');
            $pdf->SetXY(100,15);$pdf->Cell(85,3,$prestacion->Fecha,0,0,'R');
            
            $pdf->Image($path, 190, 5, 15, 15);
        }
        return $out === 'texto' ? $code : $path;
    }

    public static function generarQRPrueba($tipo, $out): mixed
    {
        $path = storage_path('app/public/temp/qr_image.png');

        $code = strtoupper('www.cmit.com.ar');

        QrCode::size(300)->format('png')->generate($code, $path);

        return $out === 'texto' ? $code : $path;
    }

}