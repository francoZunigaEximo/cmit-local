<?php

namespace App\Services\Reportes\Estudios;

use App\Helpers\Tools;
use App\Models\Cliente;
use App\Models\DatoPaciente;
use App\Models\Fichalaboral;
use App\Models\Localidad;
use App\Models\Prestacion;
use App\Models\Provincia;
use App\Models\Telefono;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use FPDF;

use DateTime;

class EXAMENREPORTE48 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E48_1.jpg"),5,9,199); 
        $pdf->Image(public_path("/archivos/reportes/E48_2.jpg"),5,169,199); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(185,273);$pdf->Cell(0,3,'1',0,0,'L'); 
        $pdf->SetXY(32,59);$pdf->Cell(0,3,'CMIT de Irigoyen Miguel Antonio',0,0,'L');$pdf->SetXY(151,59);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(35,73);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        $pdf->SetXY(135,91);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(42,96);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(43,100);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(70,100);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(91,100);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(179,100);}else{$pdf->SetXY(186,100);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(27,104);$pdf->Cell(0,3,substr($domipac,0,60),0,0,'L');
        $pdf->SetXY(30,109);$pdf->Cell(0,3,$locpac,0,0,'L');$pdf->SetXY(105,109);$pdf->Cell(0,3,$cp,0,0,'L');$pdf->SetXY(135,109);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(47,113);$pdf->Cell(0,3,$puesto,0,0,'L');
    }

    private function edad($fechaNacimiento)
    {

        $fecha_nacimiento = new DateTime($fechaNacimiento);
        // Fecha actual
        $hoy = new DateTime('now');

        // Calcular la diferencia
        $edad = $hoy->diff($fecha_nacimiento)->y;
        return $edad;
    }

    private function prestacion(int $id): mixed
    {
        return Prestacion::with(['empresa', 'paciente'])->find($id);
    }

    private function datosPaciente(int $id): mixed
    {
        return DatoPaciente::where('IdPrestacion', $id)->first();
    }

    private function telefono(int $idPaciente): mixed //IdEntidad
    {
        return Telefono::where('IdEntidad', $idPaciente)->first(['CodigoArea', 'NumeroTelefono']);
    }

    private function localidad(int $id): mixed
    {
        return Localidad::find($id);
    }
}
