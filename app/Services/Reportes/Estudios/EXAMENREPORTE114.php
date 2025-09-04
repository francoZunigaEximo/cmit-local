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

class EXAMENREPORTE114 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E114_1.jpg"),20,20,186); 
        $pdf->Image(public_path("/archivos/reportes/E114_2.jpg"),20,189,186); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(86,35);$pdf->Cell(0,3,substr($paraempresa,0,30),0,0,'L');
        $pdf->SetXY(179,35);$pdf->Cell(0,3,$antigpto,0,0,'L');
        $pdf->SetXY(63,41);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');
        $pdf->SetXY(157,41);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(63,47);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(118,47);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(164,47);$pdf->Cell(0,3,$nac,0,0,'L');
        $pdf->SetXY(52,52);$pdf->Cell(0,3,substr($domipac,0,52),0,0,'L');
        $pdf->SetXY(160,52);$pdf->Cell(0,3,substr($locpac,0,22),0,0,'L');
        $pdf->SetXY(52,58);$pdf->Cell(0,3,substr($pcia,0,30),0,0,'L');
        $pdf->SetXY(122,58);$pdf->Cell(0,3,$telpac,0,0,'L');
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
