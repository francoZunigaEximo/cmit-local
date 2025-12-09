<?php

namespace App\Services\Reportes\Cuerpos;

use App\Models\ItemPrestacion;
use App\Models\Prestacion;
use App\Services\Reportes\Reporte;
use FPDF;
use Carbon\Carbon;

class PedidoProveedores extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id']):void
    {
        $idp = str_pad($datos['id'], 8, "0", STR_PAD_LEFT);
        $y = 0;

        $prestaciones = $this->prestaciones($datos['id']);
        $itemsprestaciones = $this->itemsprestaciones($datos['id']);

        foreach($itemsprestaciones as $item) {

            $examenes = $this->examenes($item->examenes->IdProveedor, $datos['id']);
            $cantlineas = count($examenes);

            $espacioNecesario = 45 + ($cantlineas * 4); 

            if (($pdf->GetY() + $espacioNecesario) > 273) {
                $pdf->AddPage(); 
                $y = 0; 
                $controlcorte = 0;
            }

            //encabezado clte
            $pdf->SetY($y);
            $pdf->Rect(10,$y+20,112,17); $pdf->SetFont('Arial','B',8);
            $pdf->SetXY(11,$y+21);$pdf->Cell(0,3,'Paciente: '.utf8_decode($prestaciones->paciente->Apellido." ".$prestaciones->paciente->Nombre),0,0,'L');
            $pdf->SetXY(11,$y+25);$pdf->Cell(0,3,'Fecha: '.Carbon::parse($prestaciones->Fecha)->format("d/m/Y").'     '.$prestaciones->paciente->TipoDocumento.": ".$prestaciones->paciente->Documento.'     Edad: '.Carbon::parse($prestaciones->paciente->FechaNacimiento)->age.'     '.Carbon::parse($prestaciones->paciente->FechaNacimiento)->format("d/m/Y"),0,0,'L');
            $pdf->SetXY(11,$y+29);$pdf->Cell(0,3,'Direccion: '.utf8_decode(substr($prestaciones->paciente->Direccion,0,50).' -  '.$prestaciones->paciente->localidad->Nombre ?? ''.' -  '.$prestaciones->paciente->localidad->Provincia->Nombre),0,0,'L');
            $pdf->SetXY(11,$y+33);$pdf->Cell(0,3,'Empresa: '.utf8_decode($prestaciones->empresa->ParaEmpresa),0,0,'L');

            //titulo
            $pdf->SetFont('Arial','B',12);$pdf->SetXY(135,$y+20);$pdf->Cell(0,4,'SOLICITUD DE EXAMENES',0,0,'L');
            $pdf->SetFont('Arial','',8);$pdf->SetXY(177,$y+25);$pdf->Cell(0,3,$idp,0,0,'L');
            $pdf->SetFont('Arial','B',8);$pdf->SetXY(171,$y+30);$pdf->Cell(20,3,utf8_decode($prestaciones->TipoPrestacion),0,0,'R');


             //examenes x proveedor
            $pdf->Rect(10,$y+42,180,10); $pdf->SetFont('Arial','B',8);
            $pdf->SetXY(11,$y+43);$pdf->Cell(0,3,utf8_decode($item->examenes->proveedor1->Nombre),0,0,'L');$pdf->SetFont('Arial','',7);
            $pdf->SetXY(11,$y+46);$pdf->Cell(0,3,utf8_decode(substr($item->examenes->proveedor1->Direccion ?? '',0,50).' -  '.$item->examenes->proveedor1->localidad->Nombre.' -  '.$item->examenes->proveedor1->localidad->Provincia->Nombre),0,0,'L');
            $pdf->SetXY(11,$y+49);$pdf->Cell(0,3,$item->examenes->proveedor1->Telefono,0,0,'L');$pdf->SetFont('Arial','B',8);
            $pdf->SetXY(10,$y+53);$pdf->Cell(0,3,'Examenes Solicitados:',0,0,'L');$pdf->SetFont('Arial','',8);$pdf->Ln(5);	

           //mostrar examenes
            foreach($examenes as $examen){
                $pdf->SetFont('Arial','',8);$pdf->SetX(10);
                //si tiene codigo efector, lo muestro
                if(!empty($examen->Cod2)){$pdf->Cell(0,3,'- '.$examen->Cod2.' - '.utf8_decode($examen->Nombre),0,0,'L');
                }else{$pdf->Cell(0,3,'- '.$examen->Nombre,0,0,'L');}
                //
                $pdf->SetFont('Arial','',7);$pdf->SetX(95);$pdf->MultiCell(95,3,utf8_decode($examen->ObsExamen),0,'L',0,3);$pdf->Ln(1);
                //$pdf->SetFont('Arial','',7);$pdf->SetX(93);$pdf->Cell(0,3,substr($row1['ObsExamen'],0,80),0,0,'L');$pdf->Ln(4);
            }
            $pdf->Ln(4);
            //coordenada y
            $y=$pdf->GetY();$pdf->Line(10,$y,190,$y);$y=$y-14;
        }
    }


    private function prestaciones(int $id): mixed
    {
        return Prestacion::with(['paciente', 'empresa', 'paciente.localidad', 'paciente.localidad.provincia'])->find($id);
    }

    private function itemsprestaciones(int $id): mixed
    {
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
            ->join('localidades', 'proveedores.IdLocalidad', '=', 'localidades.Id')
            ->join('provincias', 'localidades.IdPcia', '=', 'provincias.Id')
            ->where('itemsprestaciones.Anulado', 0)
            ->where('itemsprestaciones.IdPrestacion', $id)
            ->groupBy('proveedores.Nombre')
            ->get();
    }

    private function examenes(int $idProveedor, int $idPrestacion): mixed
    {
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->select(
                'examenes.Nombre as Nombre',
                'examenes.Cod2 as Cod2',
                'itemsprestaciones.ObsExamen as ObsExamen',
            )
            ->where('itemsprestaciones.Anulado', 0)
            ->where('itemsprestaciones.IdPrestacion', $idPrestacion)
            ->where('examenes.IdProveedor', $idProveedor)
            ->orderBy('examenes.Nombre')
            ->get();
    }

}