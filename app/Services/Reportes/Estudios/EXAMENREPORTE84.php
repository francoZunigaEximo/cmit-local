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

class EXAMENREPORTE84 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
 
        $pdf->Image(public_path("/archivos/reportes/E84_1.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E84_2.jpg"),12,130,190); 
        $pdf->Image(public_path("/archivos/reportes/E84_3.jpg"),12,244,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(170,43);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(115,62);$pdf->Cell(0,3,substr($art,0,30),0,0,'L');
        $pdf->SetXY(115,71);$pdf->Cell(0,3,substr($paraempresa,0,40),0,0,'L');
        $pdf->SetXY(100,96);$pdf->Cell(0,3,substr($paciente,0,45),0,0,'L');
        $pdf->SetXY(130,105);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(132,115);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(165,123);$pdf->Cell(0,3,$antigpto,0,0,'L');
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
