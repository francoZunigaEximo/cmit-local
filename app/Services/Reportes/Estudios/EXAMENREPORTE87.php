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

class EXAMENREPORTE87 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
 
        $pdf->Image(public_path("/archivos/reportes/E87_1.jpg"),12,25,190); 
        $pdf->Image(public_path("/archivos/reportes/E87_2.jpg"),12,170,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(37,41);$pdf->Cell(0,3,substr($paciente,0,35),0,0,'L');
        $pdf->SetXY(125,41);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(158,41);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(90,46);$pdf->Cell(0,3,substr($puesto,0,26),0,0,'L');
        $pdf->SetXY(155,46);$pdf->Cell(0,3,$sexo,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E87_3.jpg"),12,25,190); 
        $pdf->Image(public_path("/archivos/reportes/E87_4.jpg"),12,210,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
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
