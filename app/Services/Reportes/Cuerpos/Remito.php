<?php

namespace App\Services\Reportes\Cuerpos;

use App\Models\Prestacion;
use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;

class Remito extends Reporte 
{

    public function render(FPDF $pdf, $datos = ['id']):void
    {
        $prestacion = $this->prestacion($datos['id']);

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

        $pdf->Rect(10,35,190,12);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetXY(10,37);
        $pdf->Cell(0,3,"ART: " . utf8_decode($prestacion->art->RazonSocial),0,0,'L');
        $pdf->SetXY(150,37);$pdf->Cell(0,3,"REMITO: " . $prestacion->NroCEE,0,0,'L');
        $pdf->SetXY(10,42);
        $pdf->Cell(0,3,"EMPRESA: " . utf8_decode($prestacion->empresa->RazonSocial),0,0,'L');
        $pdf->SetXY(150,42);$pdf->Cell(0,3,"MAPA: " . $prestacion->IdMapa,0,0,'L');

        $pdf->SetXY(10,45);
        $pdf->Cell(0,3,utf8_decode("Por medio de la presente le entregamos los estudios que a continuación se detallan."),0,0,'L');
    }

    private function prestacion(int $id):mixed
    {
        return Prestacion::with(['paciente', 'empresa'])->where('NroCEE' , $id)->first();
    }
}