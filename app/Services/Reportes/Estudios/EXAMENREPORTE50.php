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

class EXAMENREPORTE50 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia=false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E50_1.jpg"),14,10,182); 
        $pdf->Image(public_path("/archivos/reportes/E50_2.jpg"),14,142,182); 
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(105,40);$pdf->Cell(0,3,$paraempresa,0,0,'L');
        $pdf->SetXY(175,40);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(70,50);$pdf->Cell(0,3,$actividad,0,0,'L');
        $pdf->SetXY(48,62);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(163,62);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(50,70);$pdf->Cell(0,3,substr($lugarnac,0,20),0,0,'L');
        $pdf->SetXY(163,70);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(25,78);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(82,78);$pdf->Cell(0,3,$domipac,0,0,'L');
        $pdf->SetXY(142,78);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(48,96);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(170,96);$pdf->Cell(0,3,$antigpto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E50_3.jpg"),14,24,182); 
        $pdf->Image(public_path("/archivos/reportes/E50_4.jpg"),14,194,182); 
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
