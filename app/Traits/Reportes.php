<?php

namespace App\Traits;

use FPDF;
use App\Models\Prestacion;
use App\Models\Parametro;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

trait Reportes
{
    private static $URLPORTADA = "/archivos/reportes/portada.jpg";

    public function eEstudioCaratula(int $id): void
    {
        $prestacion = Prestacion::find($id);
        $miEmpresa = Parametro::getMiEmpresa();
        if($prestacion)
        {
            $paciente = $prestacion->paciente->Nombre ." ". $prestacion->paciente->Apellido;

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->Image(url('/').self::$URLPORTADA,1,0,209); 
            $y=220;
            $pdf->SetFont('Arial','B',14);
            $pdf->SetTextColor(255, 255, 255, 255);//white
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($paciente,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->Fecha,0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->paciente->TipoDocumento.' '.$prestacion->paciente->Documento,0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->empresa->RazonSocial,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$id,0,0,'L');$y=$y+10;
            $pdf->SetTextColor(0, 0, 0, 0);
            $pdf->Output($miEmpresa->Path4."caratula_".$id.".pdf", "F");
        }
    }
}

