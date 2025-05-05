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

class EXAMENREPORTE83 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
 
        $pdf->Image(public_path("/archivos/reportes/E83_1.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E83_2.jpg"),12,75,190); 
        $pdf->Image(public_path("/archivos/reportes/E83_3.jpg"),12,175,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(27,26);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(30,39);$pdf->Cell(0,3,substr($paraempresa,0,40),0,0,'L');
        $pdf->SetXY(38,55);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');
        $pdf->SetXY(147,55);$pdf->Cell(0,3,$cuil,0,0,'L');
        $pdf->SetXY(22,60);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(75,60);$pdf->Cell(0,3,$edad,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(169,60);}else{$pdf->SetXY(188,60);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(36,64);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(120,64);$pdf->Cell(0,3,$antig,0,0,'L');
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
