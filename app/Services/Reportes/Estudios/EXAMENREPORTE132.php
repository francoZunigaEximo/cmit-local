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

class EXAMENREPORTE132 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E132_1.jpg"),5,26,210); 
        $pdf->Image(public_path("/archivos/reportes/E132_2.jpg"),5,140,210); 
        $pdf->Image(public_path("/archivos/reportes/E132_3.jpg"),5,275,210); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //datos
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(29,49);$pdf->Cell(0,3,'CMIT de Irigoyen Miguel Antonio',0,0,'L');
        $pdf->SetXY(169,49);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(32,62);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        $pdf->SetXY(141,79);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(39,90);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(45,95);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(80,95);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(116,95);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(189,95);}else{$pdf->SetXY(198,95);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(20,99);$pdf->Cell(0,3,substr($domipac,0,60),0,0,'L');
        $pdf->SetXY(25,104);$pdf->Cell(0,3,substr($locpac,0,40),0,0,'L');
        $pdf->SetXY(46,109);$pdf->Cell(0,3,substr($puesto,0,30),0,0,'L');$pdf->SetXY(185,109);$pdf->Cell(0,3,$antigpto,0,0,'L');//pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E132_4.jpg"),5,26,210); 
        $pdf->Image(public_path("/archivos/reportes/E132_5.jpg"),5,110,210); 
        $pdf->Image(public_path("/archivos/reportes/E132_6.jpg"),5,235,210); 
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
