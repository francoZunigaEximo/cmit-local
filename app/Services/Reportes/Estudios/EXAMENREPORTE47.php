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

class EXAMENREPORTE47 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E47_1.jpg"),5,8,200); 
        $pdf->Image(public_path("/archivos/reportes/E47_2.jpg"),5,174,200); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(32,59);$pdf->Cell(0,3,'CMIT de Irigoyen Miguel Antonio',0,0,'L');$pdf->SetXY(151,59);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(35,70);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        $pdf->SetXY(136,84);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(42,89);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(43,93);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(70,93);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(91,93);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(180,93);}else{$pdf->SetXY(187,93);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(27,96);$pdf->Cell(0,3,substr($domipac,0,60),0,0,'L');
        $pdf->SetXY(30,100);$pdf->Cell(0,3,$locpac,0,0,'L');$pdf->SetXY(105,100);$pdf->Cell(0,3,$cp,0,0,'L');$pdf->SetXY(135,100);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(47,104);$pdf->Cell(0,3,$puesto,0,0,'L');
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
