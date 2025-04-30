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

class EXAMENREPORTE61 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E61_1.jpg"),10,10,172); 
        $pdf->Image(public_path("/archivos/reportes/E61_2.jpg"),10,154,172); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(29,33);$pdf->Cell(0,3,$paraempresa,0,0,'L');$pdf->SetXY(145,33);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(45,40);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(145,40);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(23,46);$pdf->Cell(0,3,$edad,0,0,'L');
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
