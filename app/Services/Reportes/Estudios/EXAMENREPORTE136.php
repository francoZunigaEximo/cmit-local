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

class EXAMENREPORTE136 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E136_1.jpg"),5,15,200); 
        $pdf->Image(public_path("/archivos/reportes/E136_2.jpg"),5,98,200); 
        $pdf->Image(public_path("/archivos/reportes/E136_3.jpg"),5,230,200); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
    
        $pdf->SetXY(155,25);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(32,39);$pdf->Cell(0,3,substr($paciente,0,33),0,0,'L');
        $pdf->SetXY(110,39);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(178,39);$pdf->Cell(0,3,$edad,0,0,'L');
        //
        $pdf->SetXY(32,45);$pdf->Cell(0,3,substr($domipac,0,33),0,0,'L');
        $pdf->SetXY(110,45);$pdf->Cell(0,3,substr($locpac,0,14),0,0,'L');
        $pdf->SetXY(140,45);$pdf->Cell(0,3,substr($cp,0,10),0,0,'L');
        $pdf->SetXY(175,45);$pdf->Cell(0,3,$telpac,0,0,'L');
        //
        $pdf->SetXY(32,51);$pdf->Cell(0,3,substr($paraempresa,0,33),0,0,'L');
        $pdf->SetXY(110,51);$pdf->Cell(0,3,substr($puesto,0,26),0,0,'L');
        $pdf->SetXY(178,51);$pdf->Cell(0,3,$antig,0,0,'L');
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
