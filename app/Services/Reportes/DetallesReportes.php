<?php 

namespace App\Services\Reportes;

use App\Models\Profesional;
use setasign\Fpdi\Fpdi;

trait DetallesReportes
{

    //Selector de titulo y texto para el cuerpo Evaluacion Resumen
    public function TipoReportePrestacion(string $tipo, ?string $tarea, ?string $puesto): array
    {
        switch ($tipo) {
            case 'INGRESO':
                return [
                    'titulo' => 'EXAMEN PREOCUPACIONAL',
                    'tipoExamen' => 'Preocupacional',
                    'tipoPuesto' => $tarea //Se refiere a que si el texto debe llevar
                ];
            case 'EGRESO':
                return [
                    'titulo' => 'EXAMEN POSTOCUPACIONAL',
                    'tipoExamen' => 'de Egreso',
                    'tipoPuesto' => $puesto
                ];
            case 'PERIODICO':
                return [
                    'titulo' => 'EXAMEN PERIODICO',
                    'tipoExamen' => 'Periódico',
                    'tipoPuesto' => $puesto
                ];
            case 'OTRO':
            case 'OCUPACIONAL':
            case 'S/C_OCUPACIONAL':
            case 'RECMED':  
                return [
                    'titulo' => 'EXAMEN LABORAL',
                    'tipoExamen' => '',
                    'tipoPuesto' => $tarea.' / '.$puesto
                ];
            case 'CARNET': 
                return [
                    'titulo' => 'EXAMEN LABORAL',
                    'tipoExamen' => '',
                    'tipoPuesto' => $tarea
                ];
            case 'ART':
                return [
                    'titulo' => 'EXAMEN LABORAL',
                    'tipoExamen' => 'Periodico',
                    'tipoPuesto' => $puesto
                ];
            default:
                return [
                    'titulo' => '',
                    'tipoExamen' => ''
                ];
        }
    }

    //Se utiliza para marcar en el reporte la calificacion o evaluación medica (campos de prestación). Usan los mismos parametros.
    // Campos Calificaciones y Evaluacion de la entidad Prestaciones
    public function calificacionMedica(string $calificacion, string $query): string
    {
        return substr($query, 0, 1) === $calificacion ? "X" : "";     
    }

    public function firmasPdf($ruta, $xf, $yf, $pdf)
    {
        //chmod($ruta, 666);
        dd($ruta);
        die();
        $imagensize = getimagesize($ruta);
        $ancho = $imagensize[0];
        $alto = $imagensize[1]; 
		
        $proporcion = intval((($ancho*20)/$alto)/2);

		$x = $xf+(24-$proporcion);
		return $pdf->Image($ruta,$x,$yf-33,0,28);//ancho sin especificar, alto 20
    }

    public function getFirmas(int $id): mixed
    {
        return Profesional::find($id);
    }

    public function mergePDFs(int $idPrestacion, array $files, string $nombre = 'sin_nombre')
    {
        $fpdi = new Fpdi();
        $idp = str_pad($idPrestacion, 8, "0", STR_PAD_LEFT);
        
        foreach ($files as $file) {
            // Si el archivo es una URL, descárgalo primero a una ubicación temporal
            if (filter_var($file, FILTER_VALIDATE_URL)) {
                // Definir la ruta temporal
                $tempFile = storage_path('app/public/temp/' . basename($file));
               
                // Descargar el archivo a la ruta temporal
                file_put_contents($tempFile, file_get_contents($file));

                // Usar la ruta temporal en lugar de la URL
                $file = $tempFile;
            }

            $pageCount = $fpdi->setSourceFile($file);

            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $fpdi->importPage($i);
                $fpdi->getTemplateSize($template);

                // Añadir página con las dimensiones correctas
                $fpdi->AddPage();
                $fpdi->useTemplate($template);


                // Reemplazar página en el PDF
                $fpdi->useTemplate($template);
                $fpdi->SetFont('Arial', '', 8);

                // Agregar ID al pie de página
                $fpdi->Cell(0, 3, $idp, 0, 0, 'L');
            }
        }

        // Guardar el archivo fusionado
        $outputPath = storage_path('app/public/temp/merge_'.$nombre.'_'.$idPrestacion.'.pdf');
        $fpdi->Output('F', $outputPath);
    }

    
}
