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

class EXAMENREPORTE117 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E117_1.jpg"),20,10,180); 
        $pdf->Image(public_path("/archivos/reportes/E117_2.jpg"),20,172,180); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(120,21);$pdf->Cell(0,3,$cuit,0,0,'L');
        $pdf->SetXY(120,25);$pdf->Cell(0,3,$cuil,0,0,'L');
        $pdf->SetXY(120,30);$pdf->Cell(0,3,$a,0,0,'L');
        $pdf->SetXY(137,50);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(48,56);$pdf->Cell(0,3,substr($paraempresa,0,50),0,0,'L');
        $pdf->SetXY(156,56);$pdf->Cell(0,3,substr($cuit,0,30),0,0,'L');
        $pdf->SetXY(60,68);$pdf->Cell(0,3,substr($paciente,0,65),0,0,'L');
        $pdf->SetXY(37,72);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(145,72);$pdf->Cell(0,3,$fi,0,0,'L');
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
