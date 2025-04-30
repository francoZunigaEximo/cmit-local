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

class EXAMENREPORTE57 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        $pdf->SetMargins(22,20,22); //left/top/right
        include('banerlogo.php');
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //presentacion
        $pdf->SetFont('Arial','',12);$pdf->SetXY(100,37);$pdf->Cell(0,4,$fechal,0,0,'R');$pdf->Ln(16);
        $y=$pdf->GetY();$pdf->Rect(20,$y-2,170,20);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Empresa: ".substr($paraempresa,0,55),0,0,'L');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Apellido y Nombre: ".substr($paciente,0,50),0,0,'L');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"DNI: ".$doc,0,0,'L');$pdf->Ln(22);
        //cuerpo
        $pdf->SetFont('Arial','B',14);$pdf->Cell(0,4,"SI / NO",0,0,'L');$pdf->Ln(20);
        $pdf->SetFont('Arial','',11);
        $pdf->MultiCell(150,5,"Doy mi consentimiento para realizar la determinacion de ".$nombreExamen." en las muestras que a tal efecto fueron obtenidas en el DIA de la fecha en este laboratorio.",0,'J',0,5);$pdf->Ln();
        $pdf->Ln(20);
        $pdf->Cell(0,10,"Firma:",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Apellido y Nombre: ".substr($paciente,0,50),0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"DNI: ".$doc,0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Fecha de Nacimiento: ".$fechanac,0,0,'L');	
        $pdf->Ln(20);
        $pdf->SetFont('Arial','B',11);$pdf->Cell(0,5,"Temperatura Recepcion de Muestra:",0,0,'L');$pdf->SetFont('Arial','',11);
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
