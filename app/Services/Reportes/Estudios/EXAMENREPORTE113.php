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

class EXAMENREPORTE113 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E113_1.jpg"),20,20,186); 
        $pdf->Image(public_path("/archivos/reportes/E113_2.jpg"),20,141,186); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(64,113);$pdf->Cell(0,3,substr($paciente,0,65),0,0,'L');
        $pdf->SetXY(50,118);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(116,118);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(158,118);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(53,123);$pdf->Cell(0,3,substr($domipac,0,50),0,0,'L');
        $pdf->SetXY(157,123);$pdf->Cell(0,3,substr($locpac,0,25),0,0,'L');
        $pdf->SetXY(53,129);$pdf->Cell(0,3,substr($pcia,0,25),0,0,'L');
        $pdf->SetXY(116,129);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(78,142);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(139,142);$pdf->Cell(0,3,substr($paraempresa,0,33),0,0,'L');
        $pdf->SetXY(78,147);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(178,147);$pdf->Cell(0,3,substr($antigpto,0,20),0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E113_3.jpg"),20,20,186); 
        $pdf->Image(public_path("/archivos/reportes/E113_4.jpg"),20,162,186); 
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
