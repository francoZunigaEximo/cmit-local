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

class EXAMENREPORTE13 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = true): void
    {   
                include('variables.php');

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }
        $pdf->Image(public_path("/archivos/reportes/E13.jpg"),25,20,172);

        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(46,60);$pdf->Cell(0,3,$paraempresa,0,0,'L');
        $pdf->SetXY(46,64);$pdf->Cell(0,3,$apellido,0,0,'L');
        $pdf->SetXY(46,68);$pdf->Cell(0,3,$nombre,0,0,'L');
        $pdf->SetXY(62,73);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(30,77);$pdf->Cell(0,3,$doc,0,0,'L');$pdf->SetXY(157,77);$pdf->Cell(0,3,$nac,0,0,'L');
        $pdf->SetXY(50,82);$pdf->Cell(0,3,$ec,0,0,'L');$pdf->SetXY(143,82);$pdf->Cell(0,3,$sexo,0,0,'L');
        $pdf->SetXY(45,86);$pdf->Cell(0,3,$domipac.' '.$locpac,0,0,'L');$pdf->SetXY(148,86);$pdf->Cell(0,3,$puesto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E13_1.jpg"),25,20,170);
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E13_2.jpg"),25,20,168);
        $pdf->SetXY(80,64);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(55,72);$pdf->Cell(0,3,$puesto,0,0,'L');$pdf->SetXY(150,72);$pdf->Cell(0,3,$antigpto,0,0,'L');
        
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
    
    }

    private function edad($fechaNacimiento){
        
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

    private function datosPaciente(int $id):mixed
    {
        return DatoPaciente::where('IdPrestacion', $id)->first();
    }

    private function telefono(int $idPaciente):mixed //IdEntidad
    {
        return Telefono::where('IdEntidad', $idPaciente)->first(['CodigoArea', 'NumeroTelefono']);
    }

    private function localidad(int $id):mixed
    {
        return Localidad::find($id);
    }

}