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

class EXAMENREPORTE63 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
       

        $pdf->Image(public_path("/archivos/reportes/E63_1.jpg"),12,15,185); 
        $pdf->Image(public_path("/archivos/reportes/E63_2.jpg"),12,155,185);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7); 
        $pdf->SetXY(30,45);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(40,57);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        $pdf->SetXY(47,65);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(58,73);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(147,73);$pdf->Cell(0,3,$cuil,0,0,'L');
        $pdf->SetXY(52,81);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(100,81);$pdf->Cell(0,3,substr($domipac,0,45),0,0,'L');
        $pdf->SetXY(45,89);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(155,89);$pdf->Cell(0,3,$antig,0,0,'L');
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
