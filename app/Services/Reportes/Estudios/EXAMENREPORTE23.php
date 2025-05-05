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

class EXAMENREPORTE23 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {   
include('variables.php');

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }
        switch ($tipo) {
            case 'INGRESO':	$y1=46;break;
            case 'PERIODICO':	$y1=52;break;
            case 'EGRESO':	$y1=57;break;
            case 'ART':	$y1=52;break;
            case 'OCUPACIONAL':	$y1=62;break;
        }
        $pdf->SetFont('Arial','B',13);$pdf->SetXY(10,32);$pdf->Cell(200,5,'ELECTROCARDIOGRAMA EN REPOSO',0,0,'C');
        $pdf->Image(public_path("/archivos/reportes/E23.jpg"),25,40,169);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(51,75);$pdf->Cell(0,3,$paciente,0,0,'L');$pdf->SetXY(157,75);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(39,81);$pdf->Cell(0,3,$puesto,0,0,'L');$pdf->SetXY(124,81);$pdf->Cell(0,3,$antig,0,0,'L');
        $pdf->SetXY(67,$y1);$pdf->Cell(0,3,'X',0,0,'L');
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