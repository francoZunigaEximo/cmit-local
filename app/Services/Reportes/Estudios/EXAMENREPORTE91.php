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

class EXAMENREPORTE91 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');

        $pdf->Image(public_path("/archivos/reportes/E91_1.jpg"),20,20,177); 
        $pdf->Image(public_path("/archivos/reportes/E91_2.jpg"),20,138,177); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(62,119);$pdf->Cell(0,3,substr($paciente,0,50),0,0,'L');
        $pdf->SetXY(52,124);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(114,124);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(150,124);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(51,129);$pdf->Cell(0,3,substr($domipac,0,45),0,0,'L');
        $pdf->SetXY(155,129);$pdf->Cell(0,3,substr($locpac,0,20),0,0,'L');
        $pdf->SetXY(51,134);$pdf->Cell(0,3,substr($pcia,0,20),0,0,'L');
        $pdf->SetXY(100,134);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(76,147);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(133,147);$pdf->Cell(0,3,substr($paraempresa,0,30),0,0,'L');
        $pdf->SetXY(168,152);$pdf->Cell(0,3,$antigpto,0,0,'L');
        $pdf->SetXY(76,152);$pdf->Cell(0,3,$puesto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E91_3.jpg"),20,20,177); 
        $pdf->Image(public_path("/archivos/reportes/E91_4.jpg"),20,139,177); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E91_5.jpg"),20,20,177); 
        $pdf->Image(public_path("/archivos/reportes/E91_6.jpg"),20,145,177); 
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
