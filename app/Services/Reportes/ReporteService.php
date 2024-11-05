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

class ReporteService
{
    public function generarReporte(string $tituloClass, ?string $subtituloClass, string $cuerpoClass, string $tipo, string $filePath, int $id, $paramsTitulo, $paramsSubtitulo = [], $paramsCuerpo): string
    {
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Instancia del título
        $titulo = new $tituloClass();
        $titulo->render($pdf, $paramsTitulo);

        // Instancia del Subtitulo
        $subtitulo = new $subtituloClass();
        $subtitulo->render($pdf, $paramsSubtitulo);

        // Instancia del cuerpo
        $cuerpo = new $cuerpoClass();
        $cuerpo->render($pdf, $paramsCuerpo);

        $pdf->Output($filePath, "F");

        // Generar el PDF y guardarlo
        if($tipo === 'imprimir'){
            return response()->json([
                    'filePath' => $filePath, 
                    'name' => 'X'.$id.'_'.now()->format('d-m-Y').'.pdf', 
                    'msg' => 'Reporte generado correctamente', 
                    'icon' => 'success'
                ]);
        }else{
            return $filePath;
        }

    }
}
