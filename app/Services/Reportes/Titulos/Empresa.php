<?php

namespace App\Services\Reportes\Titulos;

use App\Services\Reportes\Reporte;

class Empresa extends Reporte 
{
    public function render($pdf, $datos = ['cliente']): void
    {    
        $pdf->Rect(10,40,195,30);
        $pdf->SetY(43);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'CLIENTE: ',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,5,sprintf('%05d', utf8_decode($datos['cliente']->IdEmpresa)) . ' ' .utf8_decode($datos['cliente']->empresa->RazonSocial),0,0,'L');
        $pdf->Ln();
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'EMPRESA: ',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,5,utf8_decode($datos['cliente']->empresa->ParaEmpresa),0,0,'L');
        $pdf->Ln();	
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'DATOS: ',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(100,5,"DOM: ".utf8_decode(substr($datos['cliente']->empresa->Direccion,0,40)),0,0,'L');
        $pdf->Cell(80,5,"CUIT: ".utf8_decode(substr($datos['cliente']->empresa->Identificacion,0,45)),0,0,'L');
        $pdf->Ln();	
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(100,5,"LOC: ".utf8_decode(substr($datos['cliente']->empresa->localidad->Nombre,0,40)),0,0,'L');
        $pdf->Cell(80,5,"CP: ".utf8_decode(substr($datos['cliente']->empresa->localidad->CP,0,45)),0,0,'L');
        $pdf->Ln();		
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,5,"TEL: ".utf8_decode(substr($datos['cliente']->empresa->Telefono,0,40)),0,0,'L');
        $pdf->Ln(15);
    }
}