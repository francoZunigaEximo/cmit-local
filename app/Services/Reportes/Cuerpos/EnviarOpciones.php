<?php

namespace App\Services\Reportes\Cuerpos;

use App\Helpers\FileHelper;
use FPDF;
use Illuminate\Support\Facades\File;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\DetallesReportes;

// EnviarOpciones es una parte del modulo enviarInformes del sistema anterior. Se acopla esta clase a Adjuntos Generales que es nada mas que Adjuntos Prestaciones (tiene los dos nombres). Trae archivos fisicos y digitales.
// Archivo zReportes.php 

class EnviarOpciones extends Reporte
{
    const FOLDER = 'EnviarOpciones';

    use DetallesReportes;

    public function render(FPDF $pdf, $datos = ['id']): void
    {
        $adjunto = FileHelper::getFileUrl('lectura').'/'.SELF::FOLDER.'/eAdjuntos'.$datos['id'].'.pdf';
       	
        if (File::exists($adjunto)){
            File::delete($adjunto);
        }

        $this->mergePDFs($datos['id'], [$adjunto], 'eAdjuntos');
    }
}