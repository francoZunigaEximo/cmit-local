<?php 

namespace App\Services\Reportes;

use App\Models\Profesional;

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

    //Obtiene la firma y la Foto del profesional
    public function getFirmas(int $id): Profesional
    {
        return Profesional::find($id, ['Firma', 'Foto']);
    }

    public function firmasPdf($ruta, $xf, $yf, $pdf)
    {
        chmod($ruta, 666);
        $imagensize = getimagesize($ruta);
        $ancho = $imagensize[0];
        $alto = $imagensize[1]; 
		
        $proporcion = intval((($ancho*20)/$alto)/2);

		$x = $xf+(24-$proporcion);
		return $pdf->Image($ruta,$x,$yf-33,0,28);//ancho sin especificar, alto 20
    }
    
}
