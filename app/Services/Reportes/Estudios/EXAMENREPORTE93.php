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

class EXAMENREPORTE93 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');

        $pdf->Image(public_path("/archivos/reportes/E93_1.jpg"),20,20,177); 
        $pdf->Image(public_path("/archivos/reportes/E93_2.jpg"),20,139,177);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8); 
        $pdf->SetXY(62,86);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');
        $pdf->SetXY(52,91);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(114,91);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(51,96);$pdf->Cell(0,3,substr($domipac,0,23),0,0,'L');
        $pdf->SetXY(103,96);$pdf->Cell(0,3,substr($locpac,0,20),0,0,'L');
        $pdf->SetXY(51,101);$pdf->Cell(0,3,substr($pcia,0,20),0,0,'L');
        $pdf->SetXY(103,101);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(76,115);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(140,115);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(72,120);$pdf->Cell(0,3,$antigpto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E93_3.jpg"),20,20,177); 
        $pdf->Image(public_path("/archivos/reportes/E93_4.jpg"),20,142,177); 
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
