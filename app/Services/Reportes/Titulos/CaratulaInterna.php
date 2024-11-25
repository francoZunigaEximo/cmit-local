<?php

namespace App\Services\Reportes\Titulos;

use App\Models\Prestacion;
use FPDF;
use Carbon\Carbon;
use App\Services\Reportes\Reporte;

class CaratulaInterna extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id']): void
    {
        $prestacion = $this->prestacion($datos['id']);

        $paciente = $prestacion->paciente->Apellido." ".$prestacion->paciente->Nombre;

        //Llamamos directamente a
        $reducido = new Reducido;
        $reducido->render($pdf, []);

        $y=35;$pdf->Rect(10,$y,170,44); $pdf->SetFont('Arial','',14);$y=$y+5;
        $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Paciente:',0,0,'L');
        $pdf->SetXY(38,$y);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');$y=$y+8;
        $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Fecha:',0,0,'L');
        $pdf->SetXY(38,$y);$pdf->Cell(0,3,Carbon::parse($prestacion->Fecha)->format('d/m/Y'),0,0,'L');
        $pdf->SetXY(70,$y);$pdf->Cell(0,3,$prestacion->paciente->TipoDocumento.': '.$prestacion->paciente->Documento,0,0,'L');$y=$y+8;
        $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Cliente:',0,0,'L');
        $pdf->SetXY(38,$y);$pdf->Cell(0,3,substr($prestacion->empresa->RazonSocial,0,40),0,0,'L');$y=$y+8;
        $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Empresa:',0,0,'L');
        $pdf->SetXY(38,$y);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,40),0,0,'L');$y=$y+8;
        $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Prestacion:',0,0,'L');
        $pdf->SetXY(38,$y);$pdf->Cell(0,3,$datos['id'],0,0,'L');
    }

    private function prestacion(int $id): Prestacion
    {
        return Prestacion::with(['paciente', 'empresa'])->find($id);
    }
}