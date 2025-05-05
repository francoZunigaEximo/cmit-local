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

class EXAMENREPORTE127 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetMargins(22,20,22); //left/top/right
        $pdf->Image(public_path("/archivos/reportes/exterran.jpg"),10,10,40);$pdf->SetY(20);$pdf->SetFont('Arial','',12);$pdf->Line(10,28,200,28);
        //titulo
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(10,47);$pdf->Cell(200,4,"Certificado de Examen Medico",0,0,'C');	
        //cuerpo
        $pdf->SetFont('Arial','',12);
        $texto="Por la presente, se deja constancia que el/la Sr./Sra.: ".substr($paciente,0,40)." DNI/PAS numero ".$doc.", Puesto laboral ".substr($puesto,0,40).", realizo Examen Medico Periodico el dia ".$fecha." siendo su calificacion laboral:";
        $pdf->SetXY(15,65);$pdf->SetFont('Arial','',11);$pdf->MultiCell(180,8,$texto,0,'J',0,5);$pdf->Ln(14);
        //
        $pdf->SetX(15);$pdf->Cell(0,8,"Apto para la Tarea(....)",0,0,'L');$pdf->Ln();
        $pdf->SetX(15);$pdf->Cell(0,8,"Apto con Restricciones (....)",0,0,'L');$pdf->Ln();
        $pdf->SetX(15);$pdf->Cell(0,8,"Temporariamente No Apto (....)",0,0,'L');$pdf->Ln();
        $pdf->SetX(15);$pdf->Cell(0,8,"No Apto (....)",0,0,'L');$pdf->Ln(13);
        //
        $pdf->SetX(15);$pdf->Cell(0,8,"(Marque con X donde corresponda, y observaciones que considere a la derecha o al pie)
        ",0,0,'L');$pdf->Ln(14);
        //
        $pdf->SetX(15);$pdf->Cell(0,8,"Se recomienda nuevo control SI - NO",0,0,'L');$pdf->Ln(18);
        //
        $pdf->SetX(15);$pdf->Cell(0,8,"Observaciones:",0,0,'L');
        $y=$pdf->GetY();
        $y=$y+6;$pdf->Line(43,$y,200,$y);
        $y=$y+9;$pdf->Line(15,$y,200,$y);
        $y=$y+9;$pdf->Line(15,$y,200,$y);
        $y=$y+9;$pdf->Line(15,$y,200,$y);
        $y=$y+9;$pdf->Line(15,$y,200,$y);

        //firma
        $pdf->Line(40,260,85,260);$pdf->Line(125,260,170,260);
        $pdf->SetXY(40,262);$pdf->Cell(45,3,'Fecha',0,0,'C');
        $pdf->SetXY(125,262);$pdf->Cell(45,3,'Firma y sello Medico',0,0,'C');
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
