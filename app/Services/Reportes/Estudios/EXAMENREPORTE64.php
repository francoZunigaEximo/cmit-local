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

class EXAMENREPORTE64 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
       
        $pdf->Image(public_path("/archivos/reportes/E64_1.jpg"),10,13,184); 
        $pdf->Image(public_path("/archivos/reportes/E64_2.jpg"),10,188,184); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(158,49);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(33,57);$pdf->Cell(0,3,substr($paraempresa,0,40),0,0,'L');$pdf->SetXY(135,57);$pdf->Cell(0,3,$cuit,0,0,'L');
        $pdf->SetXY(30,64);$pdf->Cell(0,3,substr($domie,0,40),0,0,'L');$pdf->SetXY(121,64);$pdf->Cell(0,3,$loce,0,0,'L');
        $pdf->SetXY(42,78);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');
        $pdf->SetXY(128,78);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(46,86);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(118,86);$pdf->Cell(0,3,$sector,0,0,'L');
        $pdf->SetXY(48,94);$pdf->Cell(0,3,$antigpto,0,0,'L');$pdf->SetXY(138,94);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(30,101);$pdf->Cell(0,3,substr($domipac,0,50),0,0,'L');$pdf->SetXY(144,101);$pdf->Cell(0,3,$telpac,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E64_3.jpg"),10,9,184); 
        $pdf->Image(public_path("/archivos/reportes/E64_4.jpg"),10,25,184); 
        $pdf->Image(public_path("/archivos/reportes/E64_5.jpg"),10,205,184); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);$pdf->SetXY(35,19);$pdf->Cell(0,3,$fecha,0,0,'L');
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
