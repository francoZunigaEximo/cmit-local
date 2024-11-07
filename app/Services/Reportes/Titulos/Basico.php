<?php

namespace App\Services\Reportes\Titulos;

use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use Carbon\Carbon;

class Basico extends Reporte
{
    public function render(FPDF $pdf, $datos = ['detalles','fecha','comprobante']): void
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
        $pdf->Line(10,26,200,26);
        $pdf->SetFont('Arial','B',14);
        $pdf->SetXY(10,9);
        $pdf->Cell(200,15, $datos['detalles'],0,0,'C');
        $pdf->SetFont('Arial','',9);
        $pdf->SetXY(10,28);
        $pdf->Cell(190,5,'FECHA: '.Carbon::parse($datos['fecha'])->format('d/m/Y'),0,0,'R');
        $pdf->SetXY(10,33);$pdf->Cell(190,5,'NRO: '.$datos['comprobante'],0,0,'R');
        $pdf->Ln(6);
    }
}