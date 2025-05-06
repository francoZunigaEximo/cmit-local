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

class EXAMENREPORTE27 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');

        if ($prestacion->empresa->RF === 1) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetXY(170, 4);
            $pdf->Cell(0, 3, 'RF', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 8);
        }
        $pdf->Image(public_path("/archivos/reportes/E25.jpg"), 25, 40, 169);
        $pdf->Image(public_path("/archivos/reportes/E3_1.jpg"), 25, 20, 60);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(100, 21);
        $pdf->Cell(0, 3, 'Nro Examen:', 0, 0, 'L');
        $pdf->SetXY(100, 25);
        $pdf->Cell(0, 3, 'Apellido y Nombres: ' . substr($paciente, 0, 30), 0, 0, 'L');
        $pdf->SetXY(100, 29);
        $pdf->Cell(0, 3, 'Empresa: ' . substr($paraempresa, 0, 30), 0, 0, 'L');
        $pdf->SetXY(100, 33);
        if ($cuil != '') {
            $pdf->Cell(0, 3, 'CUIL: ' . $cuil, 0, 0, 'L');
        } else {
            $pdf->Cell(0, 3, 'CUIL: ' . $doc, 0, 0, 'L');
        }
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
