<?php

namespace App\Services\Reportes\Estudios;

use App\Helpers\Tools;
use App\Models\DatoPaciente;
use App\Models\Prestacion;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use Carbon\Carbon;
use FPDF;

class EgresoRepsol extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$REPSOL),25,20,154); //E10.jpg

        $prestacion = $this->prestacion($datos['id']);
        $datosPaciente = $this->datosPaciente($datos['id']);

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }

        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");

        $paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;

        $pdf->SetFont('Arial','B',12);$pdf->SetXY(98,28);$pdf->Cell(0,4,'Examen Medico de Egreso',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(120,43);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,35),0,0,'L');
        $pdf->SetXY(64,54);$pdf->Cell(0,3,substr($paciente,0,50),0,0,'L');
        $pdf->SetXY(74,59);$pdf->Cell(0,3,substr($prestacion->paciente->LugarNacimiento ?? '',0,20).' '.Carbon::parse($prestacion->paciente->FechaNacimiento)->format("d/m/Y"),0,0,'L');$pdf->SetXY(159,59);$pdf->Cell(0,3,Carbon::parse($prestacion->paciente->FechaNacimiento)->age,0,0,'L');
        $pdf->SetXY(51,63);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');
        $pdf->SetXY(58,67);$pdf->Cell(0,3,$datosPaciente->Tareas ?? '',0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$REPSOL1),25,20,149); //E10_1.jpg
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(168,230);$pdf->Cell(0,3,'2',0,0,'L');
        //pagina 3
        $pdf->AddPage();$pdf->Image(public_path(ReporteConfig::$REPSOL2),25,20,150); //E10_2.jpg
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(95,28);$pdf->Cell(0,4,'Examen Medico de Egreso',0,0,'L');
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