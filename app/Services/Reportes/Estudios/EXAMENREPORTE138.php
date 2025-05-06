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

class EXAMENREPORTE138 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E138_1.jpg"),5,15,200); 
        $pdf->Image(public_path("/archivos/reportes/E138_2.jpg"),5,151,200); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");    
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(175,31);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(30,58);$pdf->Cell(0,3,substr($paraempresa,0,50),0,0,'L');
        $pdf->SetXY(153,58);$pdf->Cell(0,3,$cuit,0,0,'L');
        $pdf->SetXY(24,66);$pdf->Cell(0,3,substr($domie,0,38),0,0,'L');
        $pdf->SetXY(102,66);$pdf->Cell(0,3,substr($loce,0,28),0,0,'L');
        $pdf->SetXY(167,66);$pdf->Cell(0,3,substr($pciae,0,20),0,0,'L');
        //
        $pdf->SetXY(38,79);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');
        $pdf->SetXY(128,79);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(178,79);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(24,87);$pdf->Cell(0,3,substr($domipac,0,38),0,0,'L');
        $pdf->SetXY(102,87);$pdf->Cell(0,3,substr($locpac,0,28),0,0,'L');
        $pdf->SetXY(167,87);$pdf->Cell(0,3,$telpac,0,0,'L');
        //
        $pdf->SetXY(40,131);$pdf->Cell(0,3,substr($puesto,0,50),0,0,'L');
        $pdf->SetXY(40,138);$pdf->Cell(0,3,$antig,0,0,'L');
        $pdf->SetXY(158,138);$pdf->Cell(0,3,$fi,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E138_3.jpg"),5,15,200); 
        $pdf->Image(public_path("/archivos/reportes/E138_4.jpg"),5,158,200); 
        $pdf->SetXY(20,34);$pdf->Cell(0,3,$fecha,0,0,'L');
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
