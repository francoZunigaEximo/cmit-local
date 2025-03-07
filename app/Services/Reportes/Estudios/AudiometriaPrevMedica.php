<?php

namespace App\Services\Reportes\Estudios;

use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use App\Helpers\Tools;
use App\Models\DatoPaciente;
use App\Models\Prestacion;
use Carbon\Carbon;

class AudiometriaPrevMedica extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$AUDIOMETRIAPREMEDICA0),25,40,166); 
        $pdf->Image(public_path(ReporteConfig::$AUDIOMETRIAPREMEDICA1),25,20,60);

        $prestacion = $this->prestacion($datos['id']);
        $datosPaciente = $this->datosPaciente($datos['id']);

        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");

        $paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;

        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(100,21);$pdf->Cell(0,3,'Nro Examen:',0,0,'L');
        $pdf->SetXY(100,25);$pdf->Cell(0,3,'Apellido y Nombres: '.substr($paciente,0,30),0,0,'L');
        $pdf->SetXY(100,29);$pdf->Cell(0,3,'Empresa: '.substr($prestacion->empresa->ParaEmpresa,0,30),0,0,'L');
        $pdf->SetXY(100,33);$pdf->Cell(0,3,'CUIL: '.$prestacion->paciente->Identificacion,0,0,'L');
        $pdf->SetXY(47,43);$pdf->Cell(0,3,Carbon::parse($prestacion->Fecha)->format("d/m/Y"),0,0,'L');
        $pdf->SetXY(47,47);$pdf->Cell(0,3,Carbon::parse($prestacion->paciente->FechaNacimiento)->format('d/m/Y'),0,0,'L');
        $pdf->SetXY(47,52);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');
        $pdf->SetXY(47,56);$pdf->Cell(0,3,$datosPaciente->Puesto ?? '',0,0,'L');
        $pdf->SetXY(47,61);$pdf->Cell(0,3,Carbon::parse($datosPaciente->FechaIngreso)->format('d/m/Y') ?? '',0,0,'L');
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