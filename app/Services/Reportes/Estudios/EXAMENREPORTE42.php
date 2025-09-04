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

class EXAMENREPORTE42 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia=false): void
    {
        include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E42.jpg"),25,25,160);
        $pdf->Image(public_path("/archivos/reportes/E42_P1_1.jpg"),25,48,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_P1_2.jpg"),25,210,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_1.jpg"),25,275,160);
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(185,273);$pdf->Cell(0,3,'1',0,0,'L'); 
        $pdf->SetXY(44,56);$pdf->Cell(0,3,'CMIT de Irigoyen Miguel Antonio',0,0,'L');$pdf->SetXY(147,56);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(43,61);$pdf->Cell(0,3,substr($paraempresa,0,50),0,0,'L');
        $pdf->SetXY(130,79);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(53,84);$pdf->Cell(0,3,substr($apellido,0,30).' '.substr($nombre,0,30),0,0,'L');
        $pdf->SetXY(53,88);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(81,88);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(165,88);}else{$pdf->SetXY(171,88);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(40,93);$pdf->Cell(0,3,substr($domipac,0,50),0,0,'L');
        $pdf->SetXY(44,97);$pdf->Cell(0,3,$locpac,0,0,'L');$pdf->SetXY(120,97);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(57,102);$pdf->Cell(0,3,$puesto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E42.jpg"),25,20,160);
        $pdf->Image(public_path("/archivos/reportes/E42_P2_1.jpg"),25,48,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_P2_2.jpg"),25,233,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_1.jpg"),25,275,160); 
        $pdf->SetXY(185,273);$pdf->Cell(0,3,'2',0,0,'L');
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E42.jpg"),25,20,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_P3_1.jpg"),25,48,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_P3_2.jpg"),25,124,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_1.jpg"),25,275,160); 
        $pdf->SetXY(185,273);$pdf->Cell(0,3,'3',0,0,'L');
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E42.jpg"),25,20,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_P4_1.jpg"),25,48,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_P4_2.jpg"),25,206,160); 
        $pdf->Image(public_path("/archivos/reportes/E42_1.jpg"),25,275,160); 
        $pdf->SetXY(185,273);$pdf->Cell(0,3,'4',0,0,'L');
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
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
