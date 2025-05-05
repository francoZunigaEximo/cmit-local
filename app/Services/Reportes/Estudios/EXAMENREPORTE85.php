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

class EXAMENREPORTE85 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
 
        $pdf->Image(public_path("/archivos/reportes/E85_1.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E85_2.jpg"),12,132,190); 
        $pdf->Image(public_path("/archivos/reportes/E85_3.jpg"),12,250,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(27,29);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(32,41);$pdf->Cell(0,3,substr($paraempresa,0,50),0,0,'L');
        $pdf->SetXY(38,59);$pdf->Cell(0,3,substr($paciente,0,45),0,0,'L');
        $pdf->SetXY(147,59);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(30,64);$pdf->Cell(0,3,substr($domipac,0,40),0,0,'L');
        $pdf->SetXY(124,64);$pdf->Cell(0,3,$locpac,0,0,'L');
        $pdf->SetXY(38,69);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(125,69);$pdf->Cell(0,3,$antig,0,0,'L');
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
