<?php

namespace App\Helpers;

use App\Models\ExamenCuentaIt;
use App\Models\ItemPrestacion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ToolsReportes
{
    private static $RUTATEMPORAL = "app/public/";

    public function AnexosFormulariosPrint(int $id): mixed
    {
        //verifico si hay anexos con formularios a imprimir
	    // $query="Select e.Id From itemsprestaciones ip,examenes e Where e.Id=ip.IdExamen and e.IdReporte <> 0 and ip.Anulado=0 and e.Evaluador=1 and  ip.IdPrestacion=$idprest LIMIT 1";	$rs=mysql_query($query,$conn);

        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->select('examenes.Id as Id')
                ->whereNot('examenes.IdReporte', 0)
                ->where('itemsprestaciones.Anulado', 0)
                ->where('examenes.Evaluador', 1)
                ->where('itemsprestaciones.IdPrestacion', $id)
                ->first();
    }

    public function checkExCtaImpago(int $idPrestacion): mixed
    {
        return ExamenCuentaIt::join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->where('pagosacuenta_it.IdPrestacion', $idPrestacion)->where('pagosacuenta.Pagado', 0)->count();
    }

    public function folderTempClean(): void
    {
        $deleteFiles = ['file-', 'AINF', 'merge_']; 
        
        $files = Storage::disk('public')->files('temp'); 
        
        foreach ($files as $file) {
            
            foreach ($deleteFiles as $deleteFile) {
                if (Str::startsWith(basename($file), $deleteFile)) {
            
                    Storage::disk('public')->delete($file);
                    break; 
                }
            }
        }
    }

    public function generarArchivo($spreadsheet, $nombre)
    {
        $filePath = storage_path(self::$RUTATEMPORAL.$nombre);
 
          $writer = new Xlsx($spreadsheet);
          $writer->save($filePath);
          chmod($filePath, 0777);
 
          return response()->json(['filePath' => $filePath, 'msg' => 'Se ha generado correctamente el reporte ', 'estado' => 'success']);
    }
}