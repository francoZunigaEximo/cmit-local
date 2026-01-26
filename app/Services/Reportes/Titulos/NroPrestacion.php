<?php 

namespace App\Services\Reportes\Titulos;

use App\Models\Reporte;
use FPDF;

#id es de la prestación. Se usa debajo de Titulo Reducido en Constancia de Estudio Completo (Detallado)

class NroPrestacion extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id']): void
    {
        $reducido = new Reducido;
        $reducido->render($pdf, []);

        $pdf->SetFont('Arial','',8);$pdf->SetXY(182,4);$pdf->Cell(0,3,$datos['id'],0,0,'L');
    }
}