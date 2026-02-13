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

class EXAMENREPORTE89 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
        include('variables.php');

        $pdf->Image(public_path("/archivos/reportes/E89_1.jpg"), 20, 25, 177);
        $pdf->Image(public_path("/archivos/reportes/E89_2.jpg"), 20, 151, 177);
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(62, 54);
        $pdf->Cell(0, 3, substr($paciente, 0, 45), 0, 0, 'L');
        $pdf->SetXY(160, 54);
        if ($cuil != '') {
            $pdf->Cell(0, 3, $cuil, 0, 0, 'L');
        } else {
            $pdf->Cell(0, 3, $doc, 0, 0, 'L');
        }
        $pdf->SetXY(62, 60);
        $pdf->Cell(0, 3, $fechanac, 0, 0, 'L');
        $pdf->SetXY(100, 60);
        $pdf->Cell(0, 3, $edad, 0, 0, 'L');
        $pdf->SetXY(51, 64);
        $pdf->Cell(0, 3, substr($domipac, 0, 25), 0, 0, 'L');
        $pdf->SetXY(105, 64);
        $pdf->Cell(0, 3, substr($locpac, 0, 25), 0, 0, 'L');
        $pdf->SetXY(162, 64);
        $pdf->Cell(0, 3, substr($pcia, 0, 20), 0, 0, 'L');
        $pdf->SetXY(102, 77);
        $pdf->Cell(0, 3, substr($paraempresa, 0, 45), 0, 0, 'L');
        $pdf->SetXY(76, 82);
        $pdf->Cell(0, 3, $fi, 0, 0, 'L');
        $pdf->SetXY(142, 82);
        $pdf->Cell(0, 3, $puesto, 0, 0, 'L');
        $pdf->SetXY(70, 87);
        $pdf->Cell(0, 3, $antigpto, 0, 0, 'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E89_3.jpg"), 20, 20, 177);
        $pdf->Image(public_path("/archivos/reportes/E89_4.jpg"), 20, 145, 177);
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
