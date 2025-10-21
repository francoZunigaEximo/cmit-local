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
        $datos = $this->datos($datos['id']);

        dd($datos);

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

        $pdf->SetXY(10,49);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,3,utf8_decode("Por medio de la presente le entregamos los estudios que a continuación se detallan."),0,0,'L');

        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);

        $w_paciente = 57;
        $w_dni = 25;
        $w_cuil = 25;
        $w_prestacion = 25;
        $w_examen = 57;

        $pdf->SetX(10); 
        $pdf->Cell($w_paciente, 6, utf8_decode("Paciente"), 1, 0, 'L', true);
        $pdf->Cell($w_dni, 6, utf8_decode("DNI"), 1, 0, 'C', true);
        $pdf->Cell($w_cuil, 6, utf8_decode("CUIL"), 1, 0, 'C', true);
        $pdf->Cell($w_prestacion, 6, utf8_decode("Prestacion"), 1, 0, 'L', true);
        $pdf->Cell($w_examen, 6, utf8_decode("Examen"), 1, 0, 'L', true);
        $pdf->Ln();

        $pdf->SetFillColor(255, 255, 255); 
        $pdf->SetTextColor(0, 0, 0);

        foreach ($datos as $registro) {
            $pdf->SetX(10); // Reiniciar posición X para cada fila
            $pdf->Cell($w_paciente, 6, utf8_decode($registro->paciente->Apellido.' '.$registro->paciente->Nombre), 1, 0, 'L', true);
            $pdf->Cell($w_dni, 6, $registro->paciente->Documento, 1, 0, 'C', true);
            $pdf->Cell($w_cuil, 6, $registro->paciente->Identificacion, 1, 0, 'C', true);
            $pdf->Cell($w_prestacion, 6, utf8_decode($registro->Id), 1, 0, 'L', true);
            $pdf->Cell($w_prestacion, 6, utf8_decode(), 1, 0, 'L', true);
            $pdf->Ln(); // Nueva fila
        }
    }

    private function prestacion(int $id):mixed
    {
        return Prestacion::where('NroCEE' , $id)->first();
    }

    private function datos(int $id):mixed
    {
         return Prestacion::with(['paciente', 'empresa', 'paciente', 'itemsPrestacion.examenes'])->where('NroCEE' , $id)->get();
    }
}