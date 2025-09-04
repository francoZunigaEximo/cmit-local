<?php

namespace App\Services\Reportes\Titulos;

use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use App\Models\Prestacion;
use FPDF;

class EEstudio extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id']): void
    {
        $prestacion = $this->prestacion($datos['id']);
        
        $pdf->Image(url('/').ReporteConfig::$URLPORTADA,1,0,209); 
            $y=220;
        $pdf->SetFont('Arial','B',14);
        $pdf->SetTextColor(255, 255, 255, 255);//white
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->paciente->Nombre. ' '.$prestacion->paciente->Apellido,0,28),0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->Fecha,0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->paciente->TipoDocumento.' '.$prestacion->paciente->Documento,0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->empresa->RazonSocial,0,28),0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,28),0,0,'L');$y=$y+10;
        $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->Id,0,0,'L');$y=$y+10;
        $pdf->SetTextColor(0, 0, 0, 0);
    }

    private function prestacion(int $id): mixed
    {
        return Prestacion::with(['paciente', 'empresa'])->find($id);
    }
}