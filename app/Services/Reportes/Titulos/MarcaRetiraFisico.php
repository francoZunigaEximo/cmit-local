<?php

namespace App\Services\Reportes\Titulos;

use FPDF;
use App\Services\Reportes\Reporte;

class MarcaRetiraFisico extends Reporte
{
    public function render(FPDF $pdf, $datos = ['rf']): void
    {
        if($datos['rf' ]=== 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }
    }
}