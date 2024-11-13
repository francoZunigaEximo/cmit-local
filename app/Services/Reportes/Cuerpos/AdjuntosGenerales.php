<?php

namespace App\Services\Reportes\Cuerpos;

use App\Models\ArchivoPrestacion;
use FPDF;
use App\Services\Reportes\DetallesReportes;
use App\Helpers\FileHelper;
use App\Services\Reportes\Reporte;

class AdjuntosGenerales extends Reporte
{
    const FOLDERNAME = "AdjuntosPrestacion";
    const NOMBRE = "adjGenerales";

    use DetallesReportes;

    public function render(FPDF $pdf, $datos = ['id']): void
    {
        $archivoPrestacion = $this->archivoPrestacion($datos['id']);
        $files = [];
        
        if ($archivoPrestacion) {
            foreach ($archivoPrestacion as $archivo) {
                $file = FileHelper::getFileUrl('lectura').'/'.SELF::FOLDERNAME.'/'.$archivo->Ruta;
                array_push($files, $file);
            }

           $this->mergePDFs($datos['id'], $files, SELF::NOMBRE);
        }
    }

    private function archivoPrestacion(int $id): mixed
    {
        return ArchivoPrestacion::where('IdEntidad', $id)->get();
    }
}