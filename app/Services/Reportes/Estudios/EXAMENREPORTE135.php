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

class EXAMENREPORTE135 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E135_1.jpg"),5,32,200); 
        $pdf->Image(public_path("/archivos/reportes/E135_2.jpg"),5,151,200); 
        include ("banerlogo.php");
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //datos
        $pdf->SetFont('Arial','B',11);$pdf->SetXY(10,12);$pdf->Cell(200,4,"EVALUACION OFTALMOLOGICA",0,0,'C');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(72,35);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(72,40);$pdf->Cell(0,3,substr($paciente,0,45),0,0,'L');
        $pdf->SetXY(72,45);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(72,50);$pdf->Cell(0,3,substr($paraempresa,0,45),0,0,'L');
        $pdf->SetXY(72,55);$pdf->Cell(0,3,substr($puesto,0,45),0,0,'L');
        //
        $pdf->Line(40,270,85,270);$pdf->Line(125,270,170,270);
        $pdf->SetXY(40,272);$pdf->Cell(45,3,'Firma y aclaracion del Paciente',0,0,'C');
        $pdf->SetXY(125,272);$pdf->Cell(45,3,'Firma y sello del Especialista',0,0,'C');
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
