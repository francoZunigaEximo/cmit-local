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

class EXAMENREPORTE53 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia=false): void
    {
include('variables.php');
        

        $pdf->Image(public_path("/archivos/reportes/E53_1.jpg"),10,5,195); 
        $pdf->Image(public_path("/archivos/reportes/E53_2.jpg"),10,148,195); 
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(45,51);$pdf->Cell(0,3,$paraempresa,0,0,'L');
        $pdf->SetXY(43,56);$pdf->Cell(0,3,$domie,0,0,'L');
        $pdf->SetXY(155,56);$pdf->Cell(0,3,$loce,0,0,'L');
        $pdf->SetXY(43,62);$pdf->Cell(0,3,$cpe,0,0,'L');
        $pdf->SetXY(120,62);$pdf->Cell(0,3,$cpempre,0,0,'L');
        $pdf->SetXY(160,62);$pdf->Cell(0,3,$telempre,0,0,'L');
        $pdf->SetXY(53,67);$pdf->Cell(0,3,$mailempre,0,0,'L');
        //paciente
        $pdf->SetXY(52,84);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(173,84);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(54,89);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(113,89);$pdf->Cell(0,3,$sector,0,0,'L');
        $pdf->SetXY(59,94);$pdf->Cell(0,3,$antigpto,0,0,'L');
        $pdf->SetXY(160,94);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(42,100);$pdf->Cell(0,3,$domipac,0,0,'L');
        $pdf->SetXY(155,100);$pdf->Cell(0,3,$locpac,0,0,'L');
        $pdf->SetXY(42,105);$pdf->Cell(0,3,$pcia,0,0,'L');
        $pdf->SetXY(121,105);$pdf->Cell(0,3,$cp,0,0,'L');
        $pdf->SetXY(160,105);$pdf->Cell(0,3,$telpac,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E53_3.jpg"),10,5,195); 
        $pdf->Image(public_path("/archivos/reportes/E53_4.jpg"),10,167,195); 
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
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
