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

class EXAMENREPORTE60 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E60_1.jpg"),10,5,189); 
        $pdf->Image(public_path("/archivos/reportes/E60_2.jpg"),10,139,189); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(28,40);$pdf->Cell(0,3,$art,0,0,'L');
        $pdf->SetXY(80,40);$pdf->Cell(0,3,$paraempresa,0,0,'L');
        $pdf->SetXY(168,40);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(48,58);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(162,58);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(51,66);$pdf->Cell(0,3,substr($lugarnac,0,20),0,0,'L');
        $pdf->SetXY(161,66);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(37,72);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(85,72);$pdf->Cell(0,3,$domipac,0,0,'L');
        $pdf->SetXY(160,72);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(47,91);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(168,91);$pdf->Cell(0,3,$antigpto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E60_3.jpg"),10,5,184); 
        $pdf->Image(public_path("/archivos/reportes/E60_4.jpg"),10,197,184); 
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
