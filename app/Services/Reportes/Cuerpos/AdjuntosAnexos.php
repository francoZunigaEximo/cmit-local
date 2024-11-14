<?php

namespace App\Services\Reportes\Cuerpos;

use App\Services\Reportes\Reporte;
Use FPDF;
use Illuminate\Support\Facades\DB;
use App\Helpers\FileHelper;
use App\Services\Reportes\DetallesReportes;



class AdjuntosAnexos extends Reporte
{
    use DetallesReportes;

    const FOLDERNAME = 'AdjuntosEfector';
    const NOMBRE = 'adjAnexos';

    public function render(FPDF $pdf, $datos = []): void
    {
        $archivoEfector = $this->archivosEfector($datos['id']);
        $files = [];
        
        if ($archivoEfector) {
            foreach ($archivoEfector as $archivo) {
                $file = FileHelper::getFileUrl('lectura').'/'.SELF::FOLDERNAME.'/'.$archivo->Ruta;
                array_push($files, $file);
            }

           $this->mergePDFs($datos['id'], $files, SELF::NOMBRE);
        }
    }

    private function archivosEfector(int $id): mixed
    {
        return DB::table('archivosefector')->join('itemprestaciones', 'archivosefector.IdEntidad','=','itemprestaciones.Id')
            ->join('examenes', 'itemprestaciones.IdExamen', '=', 'examenes.Id')
            ->select(
                'archivosefector.Id as Id',
                'archivosefector.IdEntidad as IdEntidad',
                'itemprestaciones.IdProveedor as IdProveedor',
                'examenes.NoImprime as NoImprime'
            )->where('examenes.Evaluador', 1)
            ->where(function($query) {
                $query->where('examenes.Evaluador', 0)
                      ->where('examenes.EvalCopia', 1);
            })
            ->where('archivosefector.IdPrestacion', $id)
            ->get();
    }
}