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

class EXAMENREPORTE33 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E33.jpg"), 25, 20, 162);
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(51, 47);
        $pdf->Cell(0, 4, $d, 0, 0, 'L');
        $pdf->SetXY(58, 47);
        $pdf->Cell(0, 4, $m, 0, 0, 'L');
        $pdf->SetXY(64, 47);
        $pdf->Cell(0, 4, $a, 0, 0, 'L');
        $pdf->SetXY(67, 53);
        $pdf->Cell(0, 3, $paciente, 0, 0, 'L');
        $pdf->SetXY(138, 53);
        if ($cuil != '') {
            $pdf->Cell(0, 3, $cuil, 0, 0, 'L');
        } else {
            $pdf->Cell(0, 3, $doc, 0, 0, 'L');
        }
        $pdf->SetXY(54, 58);
        $pdf->Cell(0, 3, $paraempresa, 0, 0, 'L');
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
