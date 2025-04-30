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

class EXAMENREPORTE46 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');

        $pdf->Image(public_path("/archivos/reportes/E46.jpg"),25,25,171); 
        $pdf->Image(public_path("/archivos/reportes/E46_1.jpg"),25,245,171);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(38,47);$pdf->Cell(0,3,$apellido,0,0,'L');$pdf->SetXY(100,47);$pdf->Cell(0,3,$nombre,0,0,'L');
        $pdf->SetXY(38,51);$pdf->Cell(0,3,$doc,0,0,'L');$pdf->SetXY(115,51);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(170,51);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(47,77);$pdf->Cell(0,3,'CMIT',0,0,'L');
        $pdf->SetXY(51,82);$pdf->Cell(0,3,$fecha,0,0,'L');
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
