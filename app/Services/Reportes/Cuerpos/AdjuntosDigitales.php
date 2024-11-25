<?php

namespace App\Services\Reportes\Cuerpos;

use App\Helpers\FileHelper;
use App\Helpers\Tools;
use App\Services\Reportes\DetallesReportes;
use App\Services\Reportes\Reporte;
use FPDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdjuntosDigitales extends Reporte
{
    use DetallesReportes;

    protected $reporteService;
    protected $outputPath;
    protected $fileNameExport;
    protected $rutainternaefectores;
    protected $rutainternainfo;
    private $tempFile;

    const NOMBRE = 'adjDigitales';

    public function __construct()
    {
        $this->outputPath = storage_path('app/public/fusionar.pdf');
        $this->fileNameExport = 'reporte-'.Tools::randomCode(15);
        $this->tempFile = 'app/public/temp/file-';
        $this->rutainternaefectores = 'AdjuntosEfector';
        $this->rutainternainfo = 'AdjuntosInformador';
    }

    //Tipo: 1 digital, 2 fisico y digital
    public function render(FPDF $fpdf, $datos = ['id', 'tipo']):void
    {
        $querys = $this->queryCombinado($datos['id']);      
        $files = [];

        foreach($querys as $query) {

            if ($query->Tipo === 1) {
                $reporteService = new \App\Services\Reportes\ReporteService();

                $file1 = $reporteService->generarReporte(
                    InformeEtapaInformador::class,
                    null,
                    null,
                    null,
                    'guardar',
                    storage_path($this->tempFile.Tools::randomCode(15).'-'.Auth::user()->name.'.pdf'),
                    null,
                    ['idItemprestacion' => $query->IdEntidad, 'idPrestacion' => $datos['id']],
                    [],
                    [],
                    [],
                    null
                );

                array_push($files, $file1);
            }
            

            if ($query->Tipo === 2) {
                $file2 = FileHelper::getFileUrl('lectura').'/'.$this->rutainternainfo.'/'.$query->Ruta;
                array_push($files, $file2);
            }

            if ($query->Tipo === 3) {

                if ($query->Tipo === 3) {
                    if ($datos['tipo'] === 1) {
                        // Para Tipo 1, solo agregar si NoImprime es 0
                        if ($query->NoImprime === 0) {
                            $file3 = FileHelper::getFileUrl('lectura') . '/' . $this->rutainternaefectores . '/' . $query->Ruta;
                            array_push($files, $file3);
                        }
                    } elseif ($datos['tipo'] === 2) {
                        // Para Tipo 2, agregar si NoImprime es 0 o 1
                        if (in_array($query->NoImprime, [0, 1])) {
                            $file3 = FileHelper::getFileUrl('lectura') . '/' . $this->rutainternaefectores . '/' . $query->Ruta;
                            array_push($files, $file3);
                        }
                    } elseif($datos['tipo'] === 3){
                        //Para Tipo 3, agregar si NoImprime es 1
                        if ($query->NoImprime === 1) {
                            $file3 = FileHelper::getFileUrl('lectura') . '/' . $this->rutainternaefectores . '/' . $query->Ruta;
                            array_push($files, $file3);
                        }
                    }
                } 
            }
            
        }

        //$this->mergePDFs($datos['idPrestacion'], $files);
       $this->mergePDFs($datos['id'], $files, SELF::NOMBRE);
        //dd($test);
    }

    private function queryCombinado(int $id):mixed
    {
        return DB::table(DB::raw('(
            SELECT 
                a.Id as Id,
                a.IdIP as IdEntidad,
                "" as Ruta,
                i.IdProveedor as IdProveedor,
                1 as Tipo,
                0 as NoImprime
            FROM itemsprestaciones_info a
            JOIN itemsprestaciones i ON a.IdIP = i.Id
            WHERE a.C1 = 0 AND a.IdP = ?
            
            UNION
            
            SELECT 
                a.Id as Id,
                a.IdEntidad as IdEntidad,
                a.Ruta as Ruta,
                i.IdProveedor as IdProveedor,
                2 as Tipo,
                0 as NoImprime
            FROM archivosinformador a
            JOIN itemsprestaciones i ON a.IdEntidad = i.Id
            WHERE a.IdPrestacion = ?
            
            UNION
            
            SELECT 
                a.Id as Id,
                a.IdEntidad as IdEntidad,
                a.Ruta as Ruta,
                i.IdProveedor as IdProveedor,
                3 as Tipo,
                e.NoImprime as NoImprime
            FROM archivosefector a
            JOIN itemsprestaciones i ON a.IdEntidad = i.Id
            JOIN examenes e ON i.IdExamen = e.Id
            WHERE e.Evaluador = 0 AND a.IdPrestacion = ?
        ) AS combined'))
        ->addBinding($id)
        ->addBinding($id)
        ->addBinding($id)
        ->orderBy('IdProveedor')
        ->orderBy('IdEntidad')
        ->orderBy('Tipo')
        ->orderBy('Id')
        ->get();
    }
        

}
