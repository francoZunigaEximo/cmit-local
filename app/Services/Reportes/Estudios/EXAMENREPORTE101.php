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

class EXAMENREPORTE101 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E101_1.jpg"),10,15,180); 
        $pdf->Image(public_path("/archivos/reportes/E101_2.jpg"),10,141,180);
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',8); 
        $pdf->SetXY(31,84);$pdf->Cell(0,3,substr($paraempresa,0,23),0,0,'L');
        $pdf->SetXY(97,84);$pdf->Cell(0,4,$fecha,0,0,'L');
        $pdf->SetXY(43,92);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(61,96);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(43,109);$pdf->Cell(0,3,substr($nombre,0,28).' '.substr($apellido,0,28),0,0,'L');
        $pdf->SetXY(162,109);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(28,113);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(91,113);$pdf->Cell(0,3,$nac,0,0,'L');
        $pdf->SetXY(47,117);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(87,117);$pdf->Cell(0,3,substr($domipac,0,50),0,0,'L');
        $pdf->SetXY(32,121);$pdf->Cell(0,3,$telpac,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E101_3.jpg"),5,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E101_4.jpg"),5,154,190); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E101_5.jpg"),5,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E101_6.jpg"),5,173,190); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E101_7.jpg"),5,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E101_8.jpg"),5,154,190); 
       if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 5
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E101_9.jpg"),5,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E101_10.jpg"),5,235,190); 
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
