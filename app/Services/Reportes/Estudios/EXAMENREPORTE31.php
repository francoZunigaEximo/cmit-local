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

class EXAMENREPORTE31 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        $pdf->SetFont('Arial','B',13);$pdf->SetXY(10,32);$pdf->Cell(200,5,'ILUMINACION INSUFICIENTE',0,0,'C');
        $pdf->Image(public_path("/archivos/reportes/E31.jpg"),25,40,170);
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(40,78);$pdf->Cell(0,3,$paraempresa,0,0,'L');
        $pdf->SetXY(58,102);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(58,107);$pdf->Cell(0,3,$doc,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(62,111);}else{$pdf->SetXY(75,111);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(58,116);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(58,120);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(58,125);$pdf->Cell(0,3,$antig,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E31_1.jpg"),25,30,164);
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->Line(40,260,85,260);$pdf->Line(125,260,170,260);
        $pdf->SetXY(40,262);$pdf->Cell(45,3,'Firma y sello del Medico',0,0,'C');
        $pdf->SetXY(125,262);$pdf->Cell(45,3,'Firma y aclaracion del Trabajador',0,0,'C');
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
