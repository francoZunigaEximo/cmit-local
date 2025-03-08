<?php

namespace App\Services\Reportes\Estudios;

use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use App\Helpers\Tools;
use App\Models\DatoPaciente;
use App\Models\Localidad;
use App\Models\Prestacion;
use Carbon\Carbon;

class AudiometriaCarley extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$AUDIOMETRIACARLEY),25,15,166);

        $prestacion = $this->prestacion($datos['id']);
        $datosPaciente = $this->datosPaciente($datos['id']);

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }

        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");

        $paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;
        $localidad = $this->localidad($datosPaciente->IdLocalidad) ?? '';

        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(44,48);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,40),0,0,'L');
        $pdf->SetXY(54,55);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');$pdf->SetXY(148,55);$pdf->Cell(0,3,Carbon::parse($prestacion->Fecha)->format("d/m/Y"),0,0,'L');
        $pdf->SetXY(44,61);$pdf->Cell(0,3,substr($datosPaciente->Direccion ?? '',0,35)." ".$localidad->Nombre,0,0,'L');
        $pdf->SetXY(148,68);$pdf->Cell(0,3,Carbon::parse($prestacion->paciente->FechaNacimiento)->age ?? '',0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->SetXY(25,240);$pdf->Cell(166,3,'15 de Noviembre de 1889 Nro 1423 - (C1130ABG) Capital Federal - Argentina',0,0,'C');
        $pdf->SetXY(25,244);$pdf->Cell(166,3,'Tel./Fax: 4304-2575 / 8353   -   4305-3642   -   4306-5023',0,0,'C');
    }

    private function prestacion(int $id): mixed
    {
        return Prestacion::with(['empresa', 'paciente'])->find($id);
    }

    private function datosPaciente(int $id):mixed
    {
        return DatoPaciente::where('IdPrestacion', $id)->first();
    }

    private function localidad(int $id):mixed
    {
        return Localidad::find($id);
    }
}