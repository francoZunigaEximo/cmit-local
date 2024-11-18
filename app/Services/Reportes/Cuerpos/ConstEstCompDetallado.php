<?php

namespace App\Services\Reportes\Cuerpos;

use App\Models\ItemPrestacion;
use FPDF;
use App\Services\Reportes\Reporte;
use App\Models\Prestacion;
use App\Services\Reportes\DetallesReportes;

class ConstEstCompDetallado extends Reporte
{
    use DetallesReportes;

    public function render(FPDF $pdf, $datos = ['id']): void
    {
        $prestacion = $this->prestacion($datos['id']);
        $itemsprestaciones = $this->itemsprestaciones($datos['id']);

        $tipoPrestacion = $this->TipoReportePrestacion($prestacion->TipoPrestacion, $prestacion->paciente->Tareas, $prestacion->paciente->Puesto);

        //texto informe
	    $texto = "Por medio de la presente dejo constancia que en el dia de la fecha el Sr/a, ".$prestacion->paciente->Apellido." ".$prestacion->paciente->Nombre.", ".$prestacion->paciente->TipoDocumento.': '.$prestacion->paciente->Documento." ha completado su examen ".$tipoPrestacion['tipoExamen']." para la empresa ".$prestacion->empresa->ParaEmpresa." que consta de los siguientes estudios:";

        //header
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,32);$pdf->Cell(200,4,$tipoPrestacion['titulo'],0,0,'C');	
        $pdf->SetFont('Arial','',10);$pdf->SetXY(190,36);$pdf->Cell(0,3,'Neuquen '.$prestacion->Fecha,0,0,'R');
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,41);$pdf->Cell(0,3,'Sres.: '.$prestacion->empresa->ParaEmpresa,0,0,'L');
        $pdf->SetFont('Arial','',10);$pdf->SetXY(10,46);$pdf->MultiCell(190,4,$texto,0,'J',0,5);
        //examenes
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,63);$pdf->Cell(0,3,'DETALLE DE ESTUDIOS',0,0,'L');$pdf->Ln(4);
        $pdf->SetFont('Arial','',7);

        //examenes
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,63);$pdf->Cell(0,3,'DETALLE DE ESTUDIOS',0,0,'L');$pdf->Ln(4);
        $pdf->SetFont('Arial','',7);

        foreach ($itemsprestaciones as $item) { 
            $pdf->SetX(15);$pdf->Cell(0,3,'- '.substr($item->examenes->Nombre,0,40),0,0,'L');
            $pdf->SetX(87);$pdf->Cell(0,3,substr($item->ObsExamen,0,75),0,0,'L');$pdf->Ln(4);
        }
        $pdf->Ln(4);
    }

    private function prestacion(int $id): mixed
    {
        return Prestacion::with(['paciente', 'empresa', 'datoPaciente'])->find($id);
    }

    private function itemsprestaciones(int $id): mixed
    {
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->where('itemsprestaciones.Anulado', 0)
            ->where('itemsprestaciones.IdPrestacion', $id)
            ->orderBy('examenes.Nombre')
            ->distinct()
            ->get();
    }
}