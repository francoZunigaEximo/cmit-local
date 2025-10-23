<?php

namespace App\Services\Reportes\Cuerpos;

use App\Helpers\FileHelper;
use App\Helpers\Tools;
use App\Models\ItemPrestacionInfo;
use App\Models\Prestacion;
use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\DetallesReportes;
use App\Services\Reportes\ReporteConfig;
use Carbon\Carbon;

class InformeEtapaInformador extends Reporte
{
    use DetallesReportes;

    public function render(FPDF $pdf, $datos = ['idItemprestacion', 'idPrestacion']): void
    {
        $itemPrestacionInfo = $this->itemPrestacionInfo($datos['idItemprestacion']);
        $prestacion = $this->prestacion($datos['idPrestacion']);

        if ($itemPrestacionInfo && $prestacion) {

            Tools::generarQR('A', $prestacion->Id, $itemPrestacionInfo->itemsprestacion->examenes->Id, $prestacion->paciente->Id, "qr");

            $pdf->SetFont('Arial','',8);$pdf->SetXY(182,4);$pdf->Cell(0,3,$itemPrestacionInfo->itemsprestacion->examenes->Id,0,0,'L');
            $pdf->Image(storage_path('app/public/temp/qr_image.png'), 180, 7, 20, 20, "png");
            $pdf->Rect(20,30,170,18);
            $pdf->SetFont('Arial','B',9);
            $pdf->SetXY(20,32);$pdf->Cell(0,3,"Paciente: ",0,0,'L');$pdf->SetXY(150,32);$pdf->Cell(0,3,"Fecha: ",0,0,'L');
            $pdf->SetXY(20,37);$pdf->Cell(0,3,"Empresa: ",0,0,'L');$pdf->SetXY(150,37);$pdf->Cell(0,3,"Prestacion: ",0,0,'L');
            $pdf->SetXY(20,42);$pdf->Cell(0,3,"DNI: ",0,0,'L');$pdf->SetXY(150,42);$pdf->Cell(0,3,"Edad: ",0,0,'L');	
            $pdf->SetFont('Arial','',9);
            $pdf->SetXY(36,32);$pdf->Cell(0,3,$prestacion->paciente->Apellido." ".$prestacion->paciente->Nombre,0,0,'L');$pdf->SetXY(170,32);$pdf->Cell(0,3,Carbon::parse($prestacion->Fecha)->format("d/m/Y"),0,0,'L');
            $pdf->SetXY(36,37);$pdf->Cell(0,3,$prestacion->empresa->ParaEmpresa,0,0,'L');$pdf->SetXY(170,37);$pdf->Cell(0,3,$prestacion->Id,0,0,'L');
            $pdf->SetXY(36,42);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');$pdf->SetXY(170,42);$pdf->Cell(0,3,Carbon::parse($prestacion->paciente->FechaNacimiento)->age,0,0,'L');
            //titulo

            ReporteConfig::marcaAguaImg($pdf);

            if($itemPrestacionInfo->C2 === 0){//multiexamen lleva titulo en el cuerpo
                $pdf->SetFont('Arial','BU',9);
                $pdf->SetXY(20,55);$pdf->Cell(0,3,'INFORME DE ESTUDIO: '.$itemPrestacionInfo->itemsprestacion->examenes->Nombre,0,0,'L');
                $y=65;
            }else{$y=55;}
            //informe
            $pdf->SetFont('Arial','',9);
            $pdf->SetLeftMargin(20);
            $pdf->SetXY(20,$y);$pdf->WriteHTML($itemPrestacionInfo->Obs);$pdf->Ln();
            //firma
            if(!empty($itemPrestacionInfo->itemsprestacion->Foto) or !empty($itemPrestacionInfo->itemsprestacion->Firma)){
                $y=$pdf->GetY();$y=$y+70;
                if($y>=290){$pdf->AddPage();$y=90;}//el alto de la pag es 290 

                $this->firmasPdf(FileHelper::getFileUrl('lectura').'/Prof/'.$itemPrestacionInfo->itemsprestacion->Foto,20,$y-19,$pdf);
                $pdf->Line(20,$y-27,70,$y-27);
                $pdf->SetFont('Arial','',8);$pdf->SetXY(20,$y-25);$pdf->WriteHTMLNonthue($firmaYsello->Firma);
            } 

        }
    }

    private function itemPrestacionInfo(int $id): mixed
    {
        return ItemPrestacionInfo::with(['itemsprestacion', 'itemsprestacion.examenes'])->where('IdIP', $id)->first();
    }

    private function prestacion(int $id): mixed
    {
        return Prestacion::with(['paciente', 'empresa'])->find($id);
    }
}
