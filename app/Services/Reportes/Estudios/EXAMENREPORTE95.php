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

class EXAMENREPORTE95 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
        include('variables.php');

        $pdf->Image(public_path("/archivos/reportes/E95_1.jpg"), 20, 20, 177);
        $pdf->Image(public_path("/archivos/reportes/E95_2.jpg"), 20, 137, 177);
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(50, 71);
        $pdf->Cell(0, 3, $fechanac, 0, 0, 'L');
        $pdf->SetXY(90, 71);
        $pdf->Cell(0, 3, $edad, 0, 0, 'L');
        $pdf->SetXY(120, 71);
        if ($cuil != '') {
            $pdf->Cell(0, 3, $cuil, 0, 0, 'L');
        } else {
            $pdf->Cell(0, 3, $doc, 0, 0, 'L');
        }
        $pdf->SetXY(60, 78);
        $pdf->Cell(0, 3, $fi, 0, 0, 'L');
        $pdf->SetXY(60, 84);
        $pdf->Cell(0, 3, $puesto, 0, 0, 'L');
        $pdf->SetXY(160, 84);
        $pdf->Cell(0, 3, $antigpto, 0, 0, 'L');
        $pdf->SetXY(53, 104);
        $pdf->Cell(0, 3, substr($domipac, 0, 50) . ' ' . $locpac, 0, 0, 'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E95_3.jpg"), 20, 20, 177);
        $pdf->Image(public_path("/archivos/reportes/E95_4.jpg"), 20, 142, 177);
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
