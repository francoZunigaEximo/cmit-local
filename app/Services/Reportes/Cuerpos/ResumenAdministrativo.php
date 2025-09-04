<?php

namespace App\Services\Reportes\Cuerpos;

use App\Models\ItemPrestacion;
use FPDF;
use App\Services\Reportes\Reporte;
use App\Models\Prestacion;
use Carbon\Carbon;

class ResumenAdministrativo extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'controlCorte']):void
    {
        $idp = str_pad($datos['id'], 8, "0", STR_PAD_LEFT);

        $prestacion = $this->prestacion($datos['id']);
        $itemsprestaciones = $this->itemsprestaciones($datos['id']);

        if ($datos['controlCorte'] === 1){
            
            $y = $pdf->GetY(); 
            $cantlineas = count($itemsprestaciones); 
            $controlcorte = $pdf->GetY();
            if (($controlcorte+60+$cantlineas*7)>273){$pdf->AddPage();$y=0;$controlcorte=0;}
        
        }else{$pdf->AddPage();$y=0;}

        //pdf
        $pdf->Rect(10,$y+15,90,22); $pdf->SetFont('Arial','B',8);
        $pdf->SetXY(11,$y+18);$pdf->Cell(0,3,'Paciente: '.$prestacion->paciente->Apellido." ".$prestacion->paciente->Nombre,0,0,'L');
        $pdf->SetXY(11,$y+23);$pdf->Cell(0,3,'Fecha: '.$prestacion->Fecha.' '.$prestacion->paciente->TipoDocumento.': '.$prestacion->paciente->Documento,0,0,'L');
        $pdf->SetXY(11,$y+28);$pdf->Cell(0,3,'Cliente: '.$prestacion->empresa->RazonSocial,0,0,'L');
        $pdf->SetXY(11,$y+33);$pdf->Cell(0,3,'Empresa: '.$prestacion->empresa->ParaEmpresa,0,0,'L');
        $pdf->SetFont('Arial','B',12);
        $pdf->SetXY(120,$y+15);$pdf->Cell(0,4,'RESUMEN ADMINISTRATIVO',0,0,'L');
        $pdf->SetXY(120,$y+22);$pdf->Cell(0,2,$prestacion->TipoPrestacion);
        $pdf->SetXY(120,$y+28);$pdf->Cell(0,2, $idp);
        $pdf->SetXY(10,$y+45);$pdf->SetFont('Arial','',8);
        
        //examenes
        foreach($itemsprestaciones as $item) { 
            $pdf->SetX(10);$pdf->Cell(0,3,'- '.substr($item->Nombre,0,40),0,0,'L');
            $pdf->SetX(93);$pdf->Cell(0,3,Carbon::parse($item->Fecha)->format('d/m/Y'),0,0,'L');
            $pdf->SetX(110);$pdf->Cell(0,3,substr($item->ObsExamen,0,60),0,0,'L');$pdf->Ln(5);
        }

    }

    private function prestacion(int $id):mixed
    {
        return Prestacion::with(['paciente', 'empresa'])->find($id);
    }

    private function itemsprestaciones(int $id):mixed
    {
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->select(
                'examenes.Nombre as Nombre',
                'itemsprestaciones.ObsExamen as ObsExamen',
                'itemsprestaciones.Fecha as Fecha',    
            )->where('itemsprestaciones.Anulado', 0)
            ->where('itemsprestaciones.IdPrestacion', $id)
            ->orderBy('examenes.Nombre')
            ->distinct()
            ->get();
    }
}