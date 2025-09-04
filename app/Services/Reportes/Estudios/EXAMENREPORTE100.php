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

class EXAMENREPORTE100 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');

        $pdf->Image(public_path("/archivos/reportes/E100_1.jpg"),10,5,180); 
        $pdf->Image(public_path("/archivos/reportes/E100_2.jpg"),10,132,180); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(30,80);$pdf->Cell(0,3,substr($paraempresa,0,23),0,0,'L');
        $pdf->SetXY(97,80);$pdf->Cell(0,4,$fecha,0,0,'L');
        $pdf->SetXY(40,89);$pdf->Cell(0,3,$tareas,0,0,'L');
        $pdf->SetXY(40,94);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(42,111);$pdf->Cell(0,3,substr($nombre,0,28).' '.substr($apellido,0,28),0,0,'L');
        $pdf->SetXY(168,111);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(26,115);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(93,115);$pdf->Cell(0,3,$nac,0,0,'L');
        $pdf->SetXY(45,120);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(89,120);$pdf->Cell(0,3,substr($domipac,0,50),0,0,'L');
        $pdf->SetXY(32,124);$pdf->Cell(0,3,$telpac,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E100_3.jpg"),5,5,180); 
        $pdf->Image(public_path("/archivos/reportes/E100_4.jpg"),5,168,180); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E100_5.jpg"),5,5,180); 
        $pdf->Image(public_path("/archivos/reportes/E100_6.jpg"),5,168,180); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E100_7.jpg"),5,5,180); 
        $pdf->Image(public_path("/archivos/reportes/E100_8.jpg"),5,171,180); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 5
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E100_9.jpg"),5,5,180); 
        $pdf->Image(public_path("/archivos/reportes/E100_10.jpg"),5,240,180); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
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
