<?php

namespace App\Services\Reportes\Cuerpos;

use App\Helpers\FileHelper;
use App\Models\Prestacion;
use App\Models\PrestacionAtributo;
use App\Services\Reportes\DetallesReportes;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\Titulos\Reducido;
use Carbon\Carbon;
use FPDF;
use Illuminate\Support\Facades\DB;

// En Id se pasa el de la tabla prestación que obtenemos del request
//$firmaeval 1:foto+sello, 2:sello (necesita para saber si lleva foto y sello o solo sello. En impresion es 0, en envios es 1)
class EvaluacionResumen extends Reporte
{
    use DetallesReportes;

    public function render(FPDF $pdf, $datos = ['id', 'firmaeval', 'opciones', 'eEstudio']): void
    {
        $query = $this->prestacion($datos['id']);

        $tiposAutorizados = ['OTRO', 'CARNET', 'REDMED', 'S/C_OCUPACIO'];

        $tipoPrestacion = $this->TipoReportePrestacion($query->TipoPrestacion, $query->paciente->Tareas, $query->paciente->Puesto);

        $texto= "El Sr/a, ".$query->paciente->Apellido." ".$query->paciente->Nombre.", ".$query->paciente->TipoDocumento." ".$query->paciente->Documento." derivado a nuestro servicio con el fin de efectuar examen ".$tipoPrestacion['tipoExamen']." para la tarea ".$tipoPrestacion['tipoPuesto']." segun los estudios detallados, ha presentado la siguiente calificacion.";

        $datos['eEstudio'] === 'si' ? $pdf->AddPage() : '';

        $pdf->SetFont('Arial','',8);$pdf->SetXY(182,4);$pdf->Cell(0,3,$datos['id'],0,0,'L');
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,32);$pdf->Cell(200,4,$tipoPrestacion['titulo'],0,0,'C');
		$pdf->SetFont('Arial','',10);$pdf->SetXY(190,36);$pdf->Cell(0,3,'Neuquen '.Carbon::parse($query->Fecha)->format("d/m/Y"),0,0,'R');
		$pdf->SetFont('Arial','B',10);$pdf->SetXY(10,41);$pdf->Cell(0,3,'Sres.: '.$query->empresa->ParaEmpresa,0,0,'L');
		$pdf->SetFont('Arial','',10);$pdf->SetXY(10,46);$pdf->MultiCell(190,4,$texto,0,'J',0,5);
		//examenes 
		$pdf->SetFont('Arial','B',10);$pdf->SetXY(10,63);$pdf->Cell(0,3,'DETALLE DE ESTUDIOS',0,0,'L');$pdf->Ln(4);
		$pdf->SetFont('Arial','',7);

        $examenes = $this->examenes($datos['id']);

        $cantExamenes = 0;
        $x1 = 10;
        $yInicioEx = $pdf->GetY();
        $yFinExCol1 = 0;
        $yFinExCol2 = 0;

        foreach($examenes as $examen) {

            $cantExamenes++;

            if ($cantExamenes == 18) {
                $x1 = 115;
                $yFinExCol1 = $pdf->GetY();
                $pdf->SetY($yInicioEx);
            }
			$pdf->SetX($x1);$pdf->Cell(0,3,' - '.substr($examen->Nombre,0,45),0,0,'L');
			//$pdf->SetX(88);$pdf->Cell(0,3,substr($row1['ObsExamen'],0,75),0,0,'L');
			$pdf->Ln(4);
        }

        $cantExamenes > 17 ? $yFinExCol2 = $pdf->GetY() : $yFinExCol1 = $pdf->GetY();
		//comparo cual columna es mas larga para tomar el $y
        $yFinExCol2 > $yFinExCol1 ? $pdf->SetY($yFinExCol2) : $pdf->SetY($yFinExCol1);
		//espacio
		$pdf->Ln(4);

        $prestacionAtributo = $this->prestacionAtributo($datos['id']);

        $y=$pdf->GetY();$yinicial=$y;
        //aca verifico si sigo en esta pagina o addpage, segun espacio cuadro
        //el alto de la pagina es 290  y cuadro+firma es 130
        if($y>150){$pdf->AddPage();$y=10;$yinicial=10;}

        //calificacion (es el campo Calificacion en la bd)
        $pdf->Rect(10,$y,190,10);
        if($prestacionAtributo !== null && $prestacionAtributo->SinEval === 0){//si la prest no lleva evaluacion, solo foto y obs
            if(in_array($query->TipoPrestacion, $tiposAutorizados) && $prestacionAtributo->SinEval === 1 && $datos['opciones'] === 'si') {
                $y=$y+6;
            }else{
                $pdf->SetFont('Arial','B',9);$y=$y+1;
                $pdf->SetXY(11,$y);$pdf->Cell(0,3,'EVALUACION MEDICA:',0,0,'L');
                $pdf->SetFont('Arial','',8);$y=$y+5;
                $pdf->SetXY(11,$y);$pdf->Cell(0,3,'SANO',0,0,'L');$pdf->Rect(22,$y-1,4,4);
                $pdf->SetXY(45,$y);$pdf->Cell(0,3,'CON AFECCION CONOCIDA PREVIAMENTE',0,0,'L');$pdf->Rect(107,$y-1,4,4);
                $pdf->SetXY(124,$y);$pdf->Cell(0,3,'CON AFECCION DETECTADA EN ESTE EXAMEN',0,0,'L');$pdf->Rect(192,$y-1,4,4);
            } 
        }else{$y=$y+6;}

        //evaluacion (es el campo Evaluacion en la bd)
        $y=$y+5;
        $pdf->Rect(10,$y,190,40);
        if($prestacionAtributo !== null && $prestacionAtributo->SinEval === 0){//si la prest no lleva evaluacion, solo foto y obs
            if(in_array($query->TipoPrestacion, $tiposAutorizados) && $prestacionAtributo->SinEval === 1 && $datos['opciones'] === 'si') {
                $y=$y+31;
            }else{
                $pdf->SetFont('Arial','B',9);$y=$y+1;
                $pdf->SetXY(11,$y);$pdf->Cell(0,3,'CALIFICACION FINAL DE APTITUD LABORAL:',0,0,'L');
                $pdf->SetFont('Arial','',8);$y=$y+5;
                $pdf->Rect(11,$y-1,4,4);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APTO',0,0,'L');
                $pdf->SetXY(31,$y);$pdf->MultiCell(125,3,'SANO SIN PRE-EXISTENCIA: Salud Ocupacional Normal',0,'L',0,3);
                $y=$y+7;
                $pdf->Rect(11,$y-1,4,4);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APTO',0,0,'L');
                $pdf->SetXY(31,$y);$pdf->MultiCell(125,3,'CON PRE-EXISTENCIA: Existen alteraciones organicas y/o funcionales permanentes, pero que por el momento no interfieren en el adecuado '.utf8_decode('desempeño').' laboral del interesado en sus tareas especificas',0,'L',0,3);
                $y=$y+11;
                $pdf->Rect(11,$y-1,4,4);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APTO',0,0,'L');
                $pdf->SetXY(31,$y);$pdf->MultiCell(125,3,'CON PRE-EXISTENCIA: Solo puede cumplir con las tareas en condiciones especiales de trabajo',0,'L',0,3);
                $y=$y+7;
                $pdf->Rect(11,$y-1,4,4);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'NO APTO',0,0,'L');
                $pdf->SetXY(31,$y);$pdf->MultiCell(125,3,'Existen alteraciones organicas y/o funcionales INCOMPATIBLES con un adecuado y saludable '.utf8_decode('desempeño').' del postulante en las tareas para las que fuera propuesto',0,'L',0,3);
            }
        }else{$y=$y+31;}

        //observaciones
        $y=$y+10;
        $pdf->Rect(10,$y,190,27);
        $pdf->SetFont('Arial','B',9);$y=$y+1;
        $pdf->SetXY(11,$y);$pdf->Cell(0,3,'CALIFICACION:',0,0,'L');
        $pdf->SetFont('Arial','',7);$y=$y+5;
        
        //la prestacion debe estar cerrada para mostrar observaciones evaluacion
        if($query->Cerrado === 1){
            $pdf->SetXY(11,$y);$pdf->MultiCell(180,3,$query->Observaciones,0,'J',0,5);
        }

        //evaluacion
        $y=$yinicial;
        $pdf->SetFont('Arial','B',10);
        
        //la prestacion debe estar cerrada para mostrar evaluacion y calificacion
        //si la prest no lleva evaluacion, solo foto y obs
        if($query->Cerrado === 1 and ($prestacionAtributo !== null && $prestacionAtributo->SinEval === 0)){	
            $pdf->SetXY(22,$y+6);$pdf->Cell(0,3,$this->calificacionMedica("1", $query->Calificacion),0,0,'L');//Reporte:evaluacion medica, Campo bd: Calificacion
            $pdf->SetXY(107,$y+6);$pdf->Cell(0,3,$this->calificacionMedica("2", $query->Calificacion),0,0,'L');
            $pdf->SetXY(192,$y+6);$pdf->Cell(0,3,$this->calificacionMedica("3", $query->Calificacion),0,0,'L');
            //calificacion
            $pdf->SetXY(11,$y+17);$pdf->Cell(0,3,$this->calificacionMedica("1", $query->Evaluacion),0,0,'L');//Reporte: calificacion final, Campo bd: Evaluacion
            $pdf->SetXY(11,$y+24);$pdf->Cell(0,3,$this->calificacionMedica("2", $query->Evaluacion),0,0,'L');
            $pdf->SetXY(11,$y+35);$pdf->Cell(0,3,$this->calificacionMedica("3", $query->Evaluacion),0,0,'L');
            $pdf->SetXY(11,$y+42);$pdf->Cell(0,3,$this->calificacionMedica("4", $query->Evaluacion),0,0,'L');	
        }

        //foto paciente
        if(!empty($query->paciente->Foto)){$pdf->Image(FileHelper::getFileUrl('lectura').'/Fotos/'.$query->paciente->Foto,160,$y+15,38,27);}


        //la prestacion debe estar cerrada para mostrar firma y sello
        //si la prest no lleva evaluacion, solo foto y obs
        if($query->Cerrado === 1){	
            //sello y firma evaluador (si tiene y no es pagina Imprimir.php)
            if($query->IdEvaluador !== 0 and ($datos['firmaeval'] === 1 or $datos['firmaeval'] === 2)){//$firmaeval 1:foto+sello, 2:sello
                //busco sello y firma
                $firmaYsello = $this->getFirmas($query->IdEvaluador);
                //muestro sello y firma
                $y=$y+112;
                if($datos['firmaeval'] === 1){
                    $this->firmasPdf(FileHelper::getFileUrl('lectura').'/Prof/'.$firmaYsello->Foto,10,$y+8,$pdf);
                    
                }//1:foto+sello	
                $pdf->Line(10,$y,60,$y);
                $pdf->SetFont('Arial','',8);$pdf->SetXY(10,$y+2);$pdf->WriteHTMLNonthue($firmaYsello->Firma);
            }
        }
    }

    
    private function prestacion(int $id): ?Prestacion
    {
        return Prestacion::with(['paciente', 'empresa', 'paciente.datopaciente',])->find($id);
    }

    private function examenes(int $id): mixed
    {
        return DB::table('itemsprestaciones as ip')
            ->join('examenes as ex', 'ex.Id', '=', 'ip.IdExamen')
            ->select('ex.Nombre as Nombre', 'ip.ObsExamen as ObsExamen')
            ->where('ip.Anulado', 0)
            ->where('ip.IdPrestacion', $id)
            ->distinct()
            ->orderBy('ex.Nombre')
            ->get();
    }

    private function prestacionAtributo(int $id): ?PrestacionAtributo
    {
        return PrestacionAtributo::where('IdPadre', $id)->first(['SinEval']);
    }

}

