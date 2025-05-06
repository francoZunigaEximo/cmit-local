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

class EXAMENREPORTE90 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
 
        $pdf->Image(public_path("/archivos/reportes/E90_1.jpg"),20,20,177); 
        $pdf->Image(public_path("/archivos/reportes/E90_2.jpg"),20,143,177); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(62,57);$pdf->Cell(0,3,substr($paciente,0,45),0,0,'L');
        $pdf->SetXY(52,62);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(130,62);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(170,62);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(51,67);$pdf->Cell(0,3,substr($domipac,0,28),0,0,'L');
        $pdf->SetXY(114,67);$pdf->Cell(0,3,substr($locpac,0,20),0,0,'L');
        $pdf->SetXY(160,67);$pdf->Cell(0,3,substr($pcia,0,20),0,0,'L');
        $pdf->SetXY(76,80);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(146,85);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(76,85);$pdf->Cell(0,3,$antigpto,0,0,'L');
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
