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

class EXAMENREPORTE66 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
       
        $pdf->Image(public_path("/archivos/reportes/E66_1.jpg"),12,15,194); 
        $pdf->Image(public_path("/archivos/reportes/E66_2.jpg"),12,95,194); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(31,62);$pdf->Cell(0,3,'Neuquen',0,0,'L');
        $pdf->SetXY(140,62);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(35,70);$pdf->Cell(0,3,substr($paraempresa,0,45),0,0,'L');
        $pdf->SetXY(138,70);$pdf->Cell(0,3,$cuit,0,0,'L');
        $pdf->SetXY(44,75);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(39,79);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        if($sexo=='Femenino'){$xsexo=121;}else{$xsexo=112;}$pdf->SetXY($xsexo,79);$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(161,79);$pdf->Cell(0,3,$fechanac,0,0,'L');
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
