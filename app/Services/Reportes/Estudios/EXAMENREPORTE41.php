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

class EXAMENREPORTE41 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia=false): void
    {
        include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E41.jpg"),25,20,156);
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(107,65);$pdf->Cell(0,3,substr($paciente,0,35),0,0,'L');
        $pdf->SetXY(76,71);$pdf->Cell(0,3,substr($paraempresa,0,20),0,0,'L');$pdf->SetXY(139,71);$pdf->Cell(0,3,substr($art,0,15),0,0,'L');
        $pdf->SetXY(55,120);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(107,185);$pdf->Cell(0,3,substr($paciente,0,35),0,0,'L');
        $pdf->SetXY(76,191);$pdf->Cell(0,3,substr($paraempresa,0,20),0,0,'L');$pdf->SetXY(139,191);$pdf->Cell(0,3,substr($art,0,15),0,0,'L');
        $pdf->SetXY(55,240);$pdf->Cell(0,3,$fecha,0,0,'L');
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
