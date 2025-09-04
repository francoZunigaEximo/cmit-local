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

class EXAMENREPORTE147 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E147_1.jpg"),5,20,195); 
        $pdf->Image(public_path("/archivos/reportes/E147_2.jpg"),5,154,195); 
        $pdf->Image(public_path("/archivos/reportes/E147_3.jpg"),5,265,195); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);    
        //datos	
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(29,42);$pdf->Cell(0,3,'SALUD OCUPACIONAL SRL',0,0,'L');
        $pdf->SetXY(158,42);$pdf->Cell(0,3,$fecha,0,0,'L');
        //
        $pdf->SetXY(32,54);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        //
        $pdf->SetXY(130,72);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(39,76);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(45,81);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(80,81);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(116,81);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(176,81);}else{$pdf->SetXY(184,81);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(21,85);$pdf->Cell(0,3,substr($domipac,0,60),0,0,'L');
        $pdf->SetXY(26,89);$pdf->Cell(0,3,substr($locpac,0,40),0,0,'L');
        $pdf->SetXY(105,89);$pdf->Cell(0,3,substr($cp,0,40),0,0,'L');
        $pdf->SetXY(46,93);$pdf->Cell(0,3,substr($puesto,0,30),0,0,'L');
        $pdf->SetXY(175,93);$pdf->Cell(0,3,$antigpto,0,0,'L');
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
