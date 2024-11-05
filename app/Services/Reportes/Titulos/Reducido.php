<?php

namespace App\Services\Reportes\Titulos;

use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;

//Constancia de Estudio Completo (Detallado)
//function PDFREPG9

class TituloReducido extends Reporte
{
    public function render(FPDF $pdf, $datos = []):void
    {
        $pdf->Image(ReporteConfig::$LOGO,10,6,20);$pdf->SetY(19);
        $pdf->SetFont('Arial','B',7);$pdf->SetX(10);$pdf->Cell(100,3,ReporteConfig::$TITULO,0,0,'L');$pdf->Ln();
        $pdf->SetFont('Arial','',7);$pdf->SetX(10);$pdf->Cell(0,3,ReporteConfig::$DIRECCION,0,0,'L');$pdf->Ln();
        $pdf->Line(10,26,200,26);
    }
}