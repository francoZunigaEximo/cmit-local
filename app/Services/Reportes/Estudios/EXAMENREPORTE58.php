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

class EXAMENREPORTE58 extends Reporte
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
        //texto
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Doy mi consentimiento para realizar el estudio de gravindex.",0,0,'L');
        //opciones
        $pdf->Ln(14);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,"SI",0,0,'L');$pdf->SetX(110);$pdf->Cell(0,10,"NO",0,0,'L');$pdf->Ln();
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(0,10,"Firma:",0,0,'L');$pdf->SetX(110);$pdf->Cell(0,10,"Firma:",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Apellido y Nombre: ".substr($paciente,0,22),0,0,'L');
        $pdf->SetX(110);$pdf->Cell(0,10,"Apellido y Nombre: ".substr($paciente,0,22),0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"DNI: ".$doc,0,0,'L');$pdf->SetX(110);$pdf->Cell(0,10,"DNI: ".$doc,0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Fecha de Nacimiento: ".$fechanac,0,0,'L');
        $pdf->SetX(110);$pdf->Cell(0,10,"Fecha de Nacimiento: ".$fechanac,0,0,'L');	$pdf->Ln();
        //rectangulo
        $pdf->Line(21,100,190,100);//arriba
        $pdf->Line(21,155,190,155);//abajo
        $pdf->Line(21,100,21,155);//izq
        $pdf->Line(105,100,105,155);//medio
        $pdf->Line(190,100,190,155);//der
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
