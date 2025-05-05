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

class EXAMENREPORTE105 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        include('banerlogo.php');
        
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetMargins(22,20,22); //left/top/right
        //presentacion
        $y=$pdf->GetY();$pdf->SetY($y-3);$y=$pdf->GetY();
        $pdf->SetFont('Arial','',12);$pdf->SetXY(100,37);$pdf->Cell(0,4,$fechal,0,0,'R');$pdf->Ln(8);
        $y=$pdf->GetY();$pdf->Rect(20,$y-2,170,20);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Empresa: ".substr($paraempresa,0,55),0,0,'L');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Apellido y Nombre: ".substr($paciente,0,50),0,0,'L');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"DNI: ".$doc,0,0,'L');$pdf->Ln(13);
        //cuerpo
        $pdf->SetFont('Arial','B',11);$pdf->Cell(0,4,"CONSENTIMIENTO INFORMADO PARA EL TESTEO DE 10 DROGAS",0,0,'C');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);
        $pdf->SetLeftMargin(30);
        $pdf->MultiCell(150,5,"En mi condicion de postulante/empleado, doy mi consentimiento a la realizacion de la prueba de drogas o sustancias prohibidas: ".$nombreExamen,0,'J',0,5);$pdf->Ln();
        $pdf->MultiCell(150,5,"Se me ha interrogado sobre posibles enfermedades y sobre tratamientos medicos actuales que pudieran generar interpretaciones erroneas de los resultados.",0,'J',0,5);$pdf->Ln();
        $pdf->MultiCell(150,5,"Entiendo que la negacion a someterme a la prueba puede tener consecuencias negativas.",0,'J',0,5);
        $pdf->Ln();
        $pdf->Cell(0,5,"Fecha: ".$fecha,0,0,'L');$pdf->Ln();
        $pdf->Cell(0,5,"Lugar: Neuquen",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,5,"Nombre del empleado: ".substr($paciente,0,45),0,0,'L');$pdf->Ln();
        $pdf->Cell(0,5,"Nro.de documento: ".$doc,0,0,'L');
        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',11);$pdf->Cell(0,5,"Temperatura Recepcion de Muestra:",0,0,'L');$pdf->SetFont('Arial','',11);
        $y=$pdf->GetY();$pdf->SetY($y+14);$y=$pdf->GetY();
        $pdf->Line(130,$y,185,$y);
        $pdf->SetXY(130,$y+2);$pdf->Cell(55,3,'Firma del empleado',0,0,'C');
        $pdf->Line(25,$y+8,185,$y+8);
        $pdf->SetY($y+10);
        $pdf->Cell(0,4,"COPIA CONFIDENCIAL",0,0,'C');$pdf->Ln();
        $pdf->Cell(0,10,"Resultado de la prueba:",0,0,'L');$pdf->Ln();
        //cuadro resultados
        $y=$pdf->GetY();//180
        $pdf->Rect(30,$y,100,55);$pdf->Line(90,$y,90,$y+55);$pdf->Line(110,$y,110,$y+55);
        $pdf->Line(30,$y+5,130,$y+5);$pdf->Line(30,$y+10,130,$y+10);$pdf->Line(30,$y+15,130,$y+15);
        $pdf->Line(30,$y+20,130,$y+20);$pdf->Line(30,$y+25,130,$y+25);$pdf->Line(30,$y+30,130,$y+30);
        $pdf->Line(30,$y+35,130,$y+35);$pdf->Line(30,$y+40,130,$y+40);$pdf->Line(30,$y+45,130,$y+45);
        $pdf->Line(30,$y+50,130,$y+50);
        $pdf->SetFont('Arial','B',11);
        $pdf->SetXY(33,$y+1);$pdf->Cell(0,3,'Droga',0,0,'L');
        $pdf->SetXY(90,$y+1);$pdf->Cell(20,3,'+',0,0,'C');$pdf->SetXY(110,$y+1);$pdf->Cell(20,3,'-',0,0,'C');
        $pdf->SetFont('Arial','B',10);
        $pdf->SetXY(33,$y+6);$pdf->Cell(0,3,'Cocaina',0,0,'L');
        $pdf->SetXY(33,$y+11);$pdf->Cell(0,3,'Marihuana',0,0,'L');
        $pdf->SetXY(33,$y+16);$pdf->Cell(0,3,'Benzodiacepina',0,0,'L');
        $pdf->SetXY(33,$y+21);$pdf->Cell(0,3,'Barbituricos',0,0,'L');
        $pdf->SetXY(33,$y+26);$pdf->Cell(0,3,'Morfina',0,0,'L');
        $pdf->SetXY(33,$y+31);$pdf->Cell(0,3,'Anfetaminas',0,0,'L');
        $pdf->SetXY(33,$y+36);$pdf->Cell(0,3,'Metilenedioximetanfetamina',0,0,'L');
        $pdf->SetXY(33,$y+41);$pdf->Cell(0,3,'Fenciclidina',0,0,'L');
        $pdf->SetXY(33,$y+46);$pdf->Cell(0,3,'Antidepresivos Triciclicos',0,0,'L');
        $pdf->SetXY(33,$y+51);$pdf->Cell(0,3,'Opiaceo',0,0,'L');
        //fin
        $y=$pdf->GetY();$pdf->SetY($y+5);$y=$pdf->GetY();$pdf->SetFont('Arial','',11);
        $pdf->Cell(0,10,"Nombre y apellido del ejecutor:",0,0,'L');$pdf->Ln();
        $y=$pdf->GetY();$pdf->SetY($y+8);$y=$pdf->GetY();
        $pdf->Line(130,$y,185,$y);
        $pdf->SetXY(130,$y+2);$pdf->Cell(55,3,'Firma',0,0,'C');
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
