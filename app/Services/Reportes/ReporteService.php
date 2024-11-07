<?php 

#tituloClass: Nombre de la clase que es caratula
#cuerpoClass: Nombre de la clase que es cuerpo
#tipo: Si es para imprimir o no
#filePath: Hay que definir la ruta a donde va a ir el archivo con sus configuracion de nombre de archivo
#id: El identificador que corresponde a cada objeto, dependiendo de si es factura, examen a cuenta o el tipo de reporte que sea
#paramsTitulo: parametros que necesita cada titulo o cuerpo dependiendo su tipo
#paramsCuerpo: lo mismo que paramsTitulo

namespace App\Services\Reportes;

use FPDF;
use setasign\Fpdi\Fpdi;

class ReporteService
{
    public function generarReporte(
        string $tituloClass,        // Siempre debe ser una clase
        ?string $subtituloClass,    // Puede ser null
        ?string $cuerpoClass,        // Siempre debe ser una clase
        string $tipo,               // Tipo de reporte
        string $filePath,           // Ruta del archivo PDF
        ?int $id,                   // ID de la prestación
        $paramsTitulo, 
        $paramsSubtitulo = [], 
        $paramsCuerpo = [],
    ): string
    {
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
    
        $titulo = new $tituloClass();
        $titulo->render($pdf, $paramsTitulo);
    
        if ($subtituloClass !== null) {
            $subtitulo = new $subtituloClass();
            $subtitulo->render($pdf, $paramsSubtitulo);
        }
    
        if ($cuerpoClass !== null) {
            $cuerpo = new $cuerpoClass();
            $cuerpo->render($pdf, $paramsCuerpo);
        }
        
        // Genera el PDF y lo guarda
        $pdf->Output($filePath, "F");
    
        if ($tipo === 'imprimir') {
            return response()->json([
                'filePath' => $filePath, 
                'name' => 'X'.$id.'_'.now()->format('d-m-Y').'.pdf', 
                'msg' => 'Reporte generado correctamente', 
                'icon' => 'success'
            ]);
        } else {
            return $filePath; 
        }
    }
    

    public function fusionarPDFs(array $filePaths, string $outputPath): string
    {
        $pdf = new FPDI();
        
        // Iterar sobre los archivos a fusionar
        foreach ($filePaths as $filePath) {
            $pageCount = $pdf->setSourceFile($filePath);
            
            // Iterar por todas las páginas de cada archivo PDF y añadirlas
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->importPage($pageNo);
                $pdf->addPage();
                $pdf->useTemplate($tplIdx);
            }
        }

        // Guardar el PDF combinado
        $pdf->Output('F', $outputPath);

        return $outputPath;
    }
}
