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

class EXAMENREPORTE151 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E151_1.jpg"),15,20,180); 
        $pdf->Image(public_path("/archivos/reportes/E151_2.jpg"),15,120,180); 
        $pdf->Image(public_path("/archivos/reportes/E151_3.jpg"),15,210,180); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");    
        //datos	
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(155,74);$pdf->Cell(0,3,$fecha,0,0,'L');
        //
        $pdf->SetXY(37,87);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        //
        $pdf->SetXY(45,128);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(30,133);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}	
        $pdf->SetXY(47,137);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(95,137);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(174,137);}else{$pdf->SetXY(182,137);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(45,142);$pdf->Cell(0,3,substr($puesto,0,30),0,0,'L');
        $pdf->SetXY(145,142);$pdf->Cell(0,3,$antigpto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E151_4.jpg"),15,20,180); 
        $pdf->Image(public_path("/archivos/reportes/E151_5.jpg"),15,130,180); 
        $pdf->Image(public_path("/archivos/reportes/E151_6.jpg"),15,230,180); 
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
