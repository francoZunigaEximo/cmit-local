<?php

namespace App\Services\Reportes\Titulos;

use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use FPDF;

class EEstudio extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id','prestacion']): void
    {
        $pdf->Image(url('/').ReporteConfig::$URLPORTADA,1,0,209); 
            $y=220;
        $pdf->SetFont('Arial','B',14);
        $pdf->SetTextColor(255, 255, 255, 255);//white
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($datos['paciente']->paciente->Nombre. ' '.$datos['paciente']->paciente->Apellido,0,28),0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,$datos['prestacion']->Fecha,0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,$datos['prestacion']->paciente->TipoDocumento.' '.$datos['prestacion']->paciente->Documento,0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($datos['prestacion']->empresa->RazonSocial,0,28),0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($datos['prestacion']->empresa->ParaEmpresa,0,28),0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,$datos['id'],0,0,'L');$y=$y+10;
        $pdf->SetTextColor(0, 0, 0, 0);
    }
}