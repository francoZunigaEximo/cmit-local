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

class EXAMENREPORTE133 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E133_1.jpg"),10,12,195); 
        $pdf->Image(public_path("/archivos/reportes/E133_2.jpg"),10,116,195); 
        $pdf->Image(public_path("/archivos/reportes/E133_3.jpg"),10,180,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");

        //datos
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(182,36);$pdf->Cell(0,3,$fecha,0,0,'L');
        //
        $pdf->SetXY(30,56);$pdf->Cell(0,3,substr($paraempresa,0,23),0,0,'L');
        $pdf->SetXY(83,56);$pdf->Cell(0,3,$fecha,0,0,'L');

        $pdf->SetXY(44,71);$pdf->Cell(0,3,substr($paciente,0,50),0,0,'L');
        $pdf->SetXY(163,71);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(46,79);$pdf->Cell(0,3,substr($lugarnac,0,35),0,0,'L');
        $pdf->SetXY(158,79);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(23,87);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(78,87);$pdf->Cell(0,3,substr($domipac,0,40),0,0,'L');
        $pdf->SetXY(161,87);$pdf->Cell(0,3,substr($telpac,0,30),0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E133_4.jpg"),5,26,195); 
        $pdf->Image(public_path("/archivos/reportes/E133_5.jpg"),5,85,195); 
        $pdf->Image(public_path("/archivos/reportes/E133_6.jpg"),5,198,195); 
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
