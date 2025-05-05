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

class EXAMENREPORTE120 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetMargins(22,20,22); //left/top/right
        //titulo
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(10,20);$pdf->Cell(200,4,"Cuestionario Evaluacion AOS en Conductores ",0,0,'C');$pdf->Ln(15);
        //paciente
        $pdf->SetFont('Arial','',11);$y=$pdf->GetY();$pdf->Rect(15,$y,180,23);$pdf->Ln();	
        $pdf->SetX(20);$pdf->Cell(0,4,"Nombre y Apellido: ".substr($paciente,0,50),0,0,'L');$pdf->Ln(6);
        $pdf->SetX(20);$pdf->Cell(0,4,"DNI: ".$doc,0,0,'L');$pdf->Ln(6);
        $pdf->SetX(20);$pdf->Cell(0,4,"Fecha: ".$fecha,0,0,'L');$pdf->Ln(20);
        //firmas
        $pdf->SetX(20);$pdf->Cell(0,4,"Firma del conductor: ............................................................................................................",0,0,'L');$pdf->Ln(15);
        $pdf->SetX(20);$pdf->Cell(0,4,"Sello y firma del medico examinador: ..................................................................................",0,0,'L');$pdf->Ln(20);	
        //cuadro
        $pdf->SetFont('Arial','B',11);$y=$pdf->GetY();$pdf->Rect(15,$y,180,142);$pdf->Ln(10);
        $pdf->SetX(10);$pdf->Cell(200,4,"Escala de Somnolencia de Epworth",0,0,'C');$pdf->Ln(12);
        $pdf->SetFont('Arial','',11);
        $pdf->SetX(20);$pdf->Cell(0,4,"El valor 0 significa que no se dormiria",0,0,'L');$pdf->Ln(5);
        $pdf->SetX(20);$pdf->Cell(0,4,"El valor 1 significa poca posibilidad de dormirse",0,0,'L');$pdf->Ln(5);
        $pdf->SetX(20);$pdf->Cell(0,4,"El valor 2 significa moderada posibilidad de dormirse",0,0,'L');$pdf->Ln(5);
        $pdf->SetX(20);$pdf->Cell(0,4,"El valor 3 significa alta posibilidad de dormirse",0,0,'L');$pdf->Ln(20);
        //titulo
        $pdf->SetFont('Arial','B',10);
        $y=$pdf->GetY();$pdf->Line(20,$y,188,$y);$pdf->Line(20,$y,20,$y+6);$pdf->Line(128,$y,128,$y+6);$pdf->Line(188,$y,188,$y+6);
        $pdf->SetXY(23,$y+2);$pdf->Cell(108,3,'Situacion',0,0,'C');
        $pdf->SetXY(126,$y+2);$pdf->Cell(60,3,'Posibilidad de dormirse',0,0,'C');
        //cuadro resultados
        $y=$y+6;$pdf->Rect(20,$y,168,61);
        //verticales
        $pdf->Line(128,$y,128,$y+61);$pdf->Line(143,$y,143,$y+61);$pdf->Line(158,$y,158,$y+61);$pdf->Line(173,$y,173,$y+61);
        //horizontales
        $pdf->Line(20,$y+6,188,$y+6);$pdf->Line(20,$y+12,188,$y+12);$pdf->Line(20,$y+18,188,$y+18);
        $pdf->Line(20,$y+27,188,$y+27);$pdf->Line(20,$y+33,188,$y+33);$pdf->Line(20,$y+42,188,$y+42);
        $pdf->Line(20,$y+48,188,$y+48);$pdf->Line(20,$y+54,188,$y+54);
        //columnas	
        $pdf->SetXY(23,$y+2);$pdf->Cell(0,3,'',0,0,'L');
        $pdf->SetXY(126,$y+2);$pdf->Cell(20,3,'0',0,0,'C');
        $pdf->SetXY(141,$y+2);$pdf->Cell(20,3,'1',0,0,'C');
        $pdf->SetXY(156,$y+2);$pdf->Cell(20,3,'2',0,0,'C');
        $pdf->SetXY(171,$y+2);$pdf->Cell(20,3,'3',0,0,'C');
        //filas
        $pdf->SetFont('Arial','',10);
        $pdf->SetXY(23,$y+8);$pdf->Cell(0,3,'Sentado leyendo',0,0,'L');
        $pdf->SetXY(23,$y+14);$pdf->Cell(0,3,'Viendo TV',0,0,'L');
        $pdf->SetXY(23,$y+20);$pdf->MultiCell(100,3,'Sentado inactivo en un lugar publico (por ejemplo un teatro o una reunion)',0,'L',0,5);
        $pdf->SetXY(23,$y+29);$pdf->Cell(0,3,'Como pasajero en un coche durante una hora, sin pausas',0,0,'L');
        $pdf->SetXY(23,$y+35);$pdf->MultiCell(100,3,'Acostado a media tarde para descansar, cuando las circunstancias lo permiten',0,'L',0,5);
        $pdf->SetXY(23,$y+44);$pdf->Cell(0,3,'Sentado charlando con alguien',0,0,'L');
        $pdf->SetXY(23,$y+50);$pdf->Cell(0,3,'Sentado tranquilamente despues de una comida sin alcohol',0,0,'L');
        $pdf->SetXY(23,$y+56);$pdf->Cell(0,3,'En un coche, parado en el trafico durante agunos minutos',0,0,'L');
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
