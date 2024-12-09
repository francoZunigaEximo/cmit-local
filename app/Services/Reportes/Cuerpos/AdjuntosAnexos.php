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

    public function render(FPDF $pdf, $datos = ['id']): void
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
        // "SELECT a.Id,a.IdEntidad,a.Ruta,i.IdProveedor,ex.NoImprime From archivosefector a,itemsprestaciones i,examenes ex where i.Id=a.IdEntidad and i.IdExamen=ex.Id and (ex.Evaluador=1 or (ex.Evaluador=0 and ex.EvalCopia=1)) and  a.IdPrestacion=$idprest Order by i.IdProveedor,a.IdEntidad,a.Id";

        return DB::table('archivosefector')->join('itemsprestaciones', 'archivosefector.IdEntidad','=','itemsprestaciones.Id')
            ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->select(
                'archivosefector.Id as Id',
                'archivosefector.IdEntidad as IdEntidad',
                'itemsprestaciones.IdProveedor as IdProveedor',
                'examenes.NoImprime as NoImprime'
            )->where(function($query) {
                $query->where('examenes.Evaluador', 1)  // Evaluador = 1
                      ->orWhere(function($query) {
                          $query->where('examenes.Evaluador', 0)  // Evaluador = 0
                                ->where('examenes.EvalCopia', 1);  // Exporta con anexos
                      });
            })
            ->where('archivosefector.IdPrestacion', $id)
            ->orderBy('itemsprestaciones.IdProveedor')
            ->orderBy('archivosefector.IdEntidad')
            ->orderBy('archivosefector.Id')
            ->get();
    }
}