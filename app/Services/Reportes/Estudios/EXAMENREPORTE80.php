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

class EXAMENREPORTE80 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
 
        $pdf->Image(public_path("/archivos/reportes/E80_1.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E80_2.jpg"),12,149,190);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(34,61);$pdf->Cell(0,3,'CMIT de Irigoyen Miguel Antonio',0,0,'L');$pdf->SetXY(152,61);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(37,72);$pdf->Cell(0,3,substr($paraempresa,0,70),0,0,'L');
        $pdf->SetXY(137,86);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(42,90);$pdf->Cell(0,3,substr($paciente,0,70),0,0,'L');
        $pdf->SetXY(45,94);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(70,94);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(93,94);$pdf->Cell(0,3,substr($nac,0,30),0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(180,94);}else{$pdf->SetXY(186,94);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(29,98);$pdf->Cell(0,3,substr($domipac,0,50),0,0,'L');
        $pdf->SetXY(34,102);$pdf->Cell(0,3,substr($locpac,0,30),0,0,'L');$pdf->SetXY(106,102);$pdf->Cell(0,3,$cp,0,0,'L');$pdf->SetXY(135,102);$pdf->Cell(0,3,substr($telpac,0,40),0,0,'L');
        $pdf->SetXY(49,105);$pdf->Cell(0,3,substr($puesto,0,30),0,0,'L');$pdf->SetXY(174,105);$pdf->Cell(0,3,$antigpto,0,0,'L');
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
