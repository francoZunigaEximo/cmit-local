<?php

namespace App\Services\Reportes\Estudios;

use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use App\Helpers\Tools;
use App\Models\DatoPaciente;
use App\Models\Prestacion;
use Carbon\Carbon;

class AudiometriaLiberty extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'],  $vistaPrevia = false): void
    {
        include('variables.php');
        $pdf->Image((public_path(ReporteConfig::$AUDIOMETRIALIBERTY)),25,15,173);

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }

        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);

        $paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;

        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(40,40);$pdf->Cell(0,3,Carbon::parse($prestacion->Fecha)->format("d/m/Y"),0,0,'L');
        $pdf->SetXY(45,45);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,60),0,0,'L');
        $pdf->SetXY(57,56);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(50,60);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');$pdf->SetXY(130,60);$pdf->Cell(0,3,$prestacion->paciente->Identificacion,0,0,'L');
        $pdf->SetXY(50,64);$pdf->Cell(0,3,Carbon::parse($prestacion->paciente->FechaNacimiento)->format("d/m/Y"),0,0,'L');
        $pdf->SetXY(56,69);$pdf->Cell(0,3,$datosPaciente->Puesto ?? '',0,0,'L');$pdf->SetXY(147,69);$pdf->Cell(0,3,Carbon::parse($datosPaciente->FechaIngreso)->age ?? '',0,0,'L');
        $pdf->Line(40,255,85,255);$pdf->Line(125,255,170,255);$pdf->SetFont('Arial','B',8);
        $pdf->SetXY(40,257);$pdf->Cell(45,3,'Firma del Trabajador',0,0,'C');
        $pdf->SetXY(125,257);$pdf->Cell(45,3,'Firma del Fonoaudiologo',0,0,'C');

    }

    private function prestacion(int $id): mixed
    {
        return Prestacion::with(['empresa', 'paciente'])->find($id);
    }

    private function datosPaciente(int $id):mixed
    {
        return DatoPaciente::where('IdPrestacion', $id)->first();
    }
}