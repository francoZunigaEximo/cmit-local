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

class EXAMENREPORTE99 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');

        $pdf->Image(public_path("/archivos/reportes/E99_1.jpg"),10,20,196); 
        $pdf->Image(public_path("/archivos/reportes/E99_2.jpg"),10,165,196); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);$pdf->SetXY(182,28);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(43,46);$pdf->Cell(0,3,substr($nombre,0,28).' '.substr($apellido,0,28),0,0,'L');
        $pdf->SetXY(43,51);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(25,56);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(90,56);$pdf->Cell(0,3,$doc,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E99_3.jpg"),10,20,196); 
        $pdf->Image(public_path("/archivos/reportes/E99_4.jpg"),10,130,196); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E99_5.jpg"),10,20,196); 
        $pdf->Image(public_path("/archivos/reportes/E99_6.jpg"),10,165,196); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E99_7.jpg"),10,20,196); 
        $pdf->Image(public_path("/archivos/reportes/E99_8.jpg"),10,190,196); 
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
