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

class EXAMENREPORTE98 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
        include('variables.php');

        include ("banerlogo.php");
       if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetMargins(22,20,22); //left/top/right
        //presentacion
        $pdf->SetFont('Arial','',12);$pdf->SetXY(100,37);$pdf->Cell(0,4,$fechal,0,0,'R');$pdf->Ln(12);
        $y=$pdf->GetY();$pdf->Rect(20,$y-2,170,20);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Empresa: ".substr($paraempresa,0,55),0,0,'L');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Apellido y Nombre: ".substr($paciente,0,50),0,0,'L');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"DNI: ".$doc,0,0,'L');$pdf->Ln(18);
        //cuerpo
        $pdf->SetFont('Arial','B',11);$pdf->Cell(0,4,"CONSENTIMIENTO INFORMADO PARA EL TESTEO DE DROGAS",0,0,'C');$pdf->Ln(15);
        $pdf->SetFont('Arial','',11);
        $pdf->SetLeftMargin(30);
        $pdf->MultiCell(150,5,"En mi condicion de postulante/empleado, doy mi consentimiento a la realizacion de la prueba de drogas o sustancias prohibidas: ".$nombreExamen,0,'J',0,5);$pdf->Ln();
        $pdf->MultiCell(150,5,"Se me ha interrogado sobre posibles enfermedades y sobre tratamientos medicos actuales que pudieran generar interpretaciones erroneas de los resultados.",0,'J',0,5);$pdf->Ln();
        $pdf->MultiCell(150,5,"Entiendo que la negacion a someterme a la prueba puede tener consecuencias negativas.",0,'J',0,5);
        $pdf->Ln(6);
        $pdf->Cell(0,5,"Fecha: ".$fecha,0,0,'L');$pdf->Ln();
        $pdf->Cell(0,5,"Lugar: Neuquen",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,5,"Nombre del empleado: ".substr($paciente,0,45),0,0,'L');$pdf->Ln();
        $pdf->Cell(0,5,"Nro.de documento: ".$doc,0,0,'L');
        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',11);$pdf->Cell(0,5,"Temperatura Recepcion de Muestra:",0,0,'L');$pdf->SetFont('Arial','',11);
        $y=$pdf->GetY();$pdf->SetY($y+24);$y=$pdf->GetY();
        $pdf->Line(130,$y,185,$y);
        $pdf->SetXY(130,$y+2);$pdf->Cell(55,3,'Firma del empleado',0,0,'C');
        $pdf->Line(25,$y+12,185,$y+12);
        $pdf->SetY($y+20);
        $pdf->Cell(0,4,"COPIA CONFIDENCIAL",0,0,'C');$pdf->Ln(10);
        $pdf->Cell(0,10,"Resultado de la prueba:",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Nombre y apellido del ejecutor:",0,0,'L');$pdf->Ln();
        $y=$pdf->GetY();$pdf->SetY($y+22);$y=$pdf->GetY();
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
