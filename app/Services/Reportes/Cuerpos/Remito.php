<?php

namespace App\Services\Reportes\Cuerpos;

use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;

class Remito extends Reporte 
{

    public function render(FPDF $pdf, $datos = ['id']):void
    {
        $pdf->Image(public_path(ReporteConfig::$LOGO),10,6,20);
        $pdf->SetY(19);
        $pdf->SetFont('Arial','B',7);
        $pdf->SetX(10);
        $pdf->Cell(100,3,ReporteConfig::$TITULO,0,0,'L');
        $pdf->Ln();

        $pdf->SetFont('Arial','',7);
        $pdf->SetX(10);
        $pdf->Cell(0,3, ReporteConfig::$DIRECCION,0,0,'L');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(10, 7);
        $pdf->Cell(200, 15, "REMITO DE ENTREGA DE ESTUDIOS", 0, 0, 'C'); // Centrado

        $anchoPagina = $pdf->GetPageWidth();
        $pdf->Code39(155,12,$datos['id'],1,5);

        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Line(10, 30, $anchoPagina - 10, 30);

        $pdf->SetY(35);

    }
}