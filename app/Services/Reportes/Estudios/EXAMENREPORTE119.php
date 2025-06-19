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

class EXAMENREPORTE119 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetMargins(22,20,22); //left/top/right
        //titulo
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(10,20);$pdf->Cell(200,4,"SUPERINTENDENCIA DE RIESGOS DEL TRABAJO",0,0,'C');$pdf->Ln(8);
        $pdf->SetFont('Arial','B',12);$pdf->SetX(10);$pdf->Cell(200,4,"Res 492-99 FORMULARIO 4",0,0,'C');$pdf->Ln(12);
        //cuadro
        $y=$pdf->GetY();$pdf->Rect(15,$y-2,180,240);$pdf->Ln();
        $pdf->SetFont('Arial','',11);
        $pdf->SetX(10);$pdf->Cell(200,4,"CONSTANCIA DE APTITUD DEL POSTULANTE",0,0,'C');$pdf->Ln(14);
        //datos	
        $pdf->SetX(20);$pdf->Cell(0,4,"Lugar y Fecha: ".$fechal,0,0,'L');$pdf->Ln(10);
        $pdf->SetX(20);$pdf->Cell(0,4,"Empresa: ".substr($paraempresa,0,55),0,0,'L');$pdf->Ln(10);
        $pdf->SetX(20);$pdf->Cell(0,4,"Postulante: ".substr($paciente,0,50),0,0,'L');$pdf->Ln(10);
        $pdf->SetX(20);$pdf->Cell(0,4,"CUIL o  DNI/LC/LE: ".$doc,0,0,'L');$pdf->Ln(10);
        $pdf->SetX(20);$pdf->Cell(0,4,"Tareas propuestas: ".substr($tareas,0,50),0,0,'L');$pdf->Ln(10);
        $pdf->SetX(20);$pdf->Cell(0,4,"Grupo Sanguineo: ",0,0,'L');$pdf->Ln(10);
        $pdf->SetX(20);$pdf->Cell(0,4,"Factor R. H: ",0,0,'L');$pdf->Ln(14);
        //informe
        $pdf->SetX(20);$pdf->Cell(0,10,"Se informa sobre la base de los resultados obtenidos en el examen medico                   ",0,0,'L');$pdf->Ln();
        $pdf->SetX(20);$pdf->Cell(0,10,"realizado el                    al postulante mencionado precedentemente y de las tareas propuestas",0,0,'L');$pdf->Ln();
        $pdf->SetX(20);$pdf->Cell(0,10,"mencionadas por Ud., se concluye que el mismo se encuentra:",0,0,'L');$pdf->Ln(20);
        //resultados apto
        $y=$pdf->GetY();$pdf->Rect(20,$y-1,4,4);$pdf->SetXY(24,$y);$pdf->Cell(0,3,'APTO',0,0,'L');
        $y=$pdf->GetY();$pdf->SetXY(50,$y);$pdf->Cell(0,3,'SIN PRE EXISTENCIAS',0,0,'L');$pdf->Rect(100,$y-1,4,4);
        $pdf->Ln(10);
        $y=$pdf->GetY();$pdf->SetXY(50,$y);$pdf->Cell(0,3,'CON PRE EXISTENCIAS',0,0,'L');$pdf->Rect(100,$y-1,4,4);
        $pdf->Ln(18);
        //resultados no apto
        $y=$pdf->GetY();$pdf->Rect(20,$y-1,4,4);$pdf->SetXY(24,$y);$pdf->Cell(0,3,'NO APTO',0,0,'L');$pdf->Ln(20);
        //firma
        $y=$pdf->GetY();$pdf->SetY($y+20);$y=$pdf->GetY();
        $pdf->Line(110,$y,180,$y);$pdf->Ln(2);
        $pdf->SetX(110);$pdf->Cell(0,6,'Dr. ',0,0,'L');$pdf->Ln();
        $pdf->SetX(110);$pdf->Cell(0,6,'Especialista en Medicina del Trabajo',0,0,'L');$pdf->Ln();
        $pdf->SetX(110);$pdf->Cell(0,6,'MN',0,0,'L');$pdf->Ln();
        $pdf->SetX(110);$pdf->Cell(0,6,'Mat.Nro      - Libro      - Folio   ',0,0,'L');
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
