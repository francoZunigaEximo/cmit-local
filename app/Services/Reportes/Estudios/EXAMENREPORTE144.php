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

class EXAMENREPORTE144 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E144_1.jpg"),15,20,150); 
        $pdf->Image(public_path("/archivos/reportes/E144_2.jpg"),15,154,150); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);    
        $pdf->SetFont('Arial','',7);

        $pdf->SetXY(55,50);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(88,58);$pdf->Cell(0,3,substr($paraempresa,0,45),0,0,'L');
        //
        $pdf->SetXY(53,83);$pdf->Cell(0,3,substr($paciente,0,50),0,0,'L');
        $pdf->SetXY(30,91);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(72,91);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(115,91);$pdf->Cell(0,3,substr($domipac,0,45),0,0,'L');
        $pdf->SetXY(38,99);$pdf->Cell(0,3,substr($locpac,0,30),0,0,'L');
        $pdf->SetXY(115,99);$pdf->Cell(0,3,substr($pcia,0,30),0,0,'L');
        $pdf->SetXY(55,108);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(87,108);$pdf->Cell(0,3,$edad,0,0,'L');
        //
        $pdf->SetXY(45,141);$pdf->Cell(0,3,substr($puesto,0,40),0,0,'L');
        $pdf->SetXY(42,157);$pdf->Cell(0,3,$antig,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E144_3.jpg"),15,20,150); 
        $pdf->Image(public_path("/archivos/reportes/E144_4.jpg"),15,160,150); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);    
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E144_5.jpg"),15,20,150); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);    
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
