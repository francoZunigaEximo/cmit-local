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

class EXAMENREPORTE56 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        include('banerlogo.php');

        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetMargins(22,20,22); //left/top/right
        //presentacion
        $pdf->SetFont('Arial','',12);$pdf->SetXY(100,30);$pdf->Cell(0,4,$fechal,0,0,'R');$pdf->Ln(10);
        $y=$pdf->GetY();$pdf->Rect(20,$y-2,170,20);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Empresa: ".substr($paraempresa,0,55),0,0,'L');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"Apellido y Nombre: ".substr($paciente,0,50),0,0,'L');$pdf->Ln(6);
        $pdf->SetFont('Arial','',11);$pdf->Cell(0,4,"DNI: ".$doc,0,0,'L');$pdf->Ln(14);
        //cuerpo
        $pdf->SetFont('Arial','B',11);$pdf->Cell(0,5,"CONSENTIMIENTO INFORMADO PARA EL TESTEO DE ALCOHOL",0,0,'C');$pdf->Ln(8);
        $pdf->SetFont('Arial','',11);
        $pdf->SetLeftMargin(22);
        $pdf->MultiCell(168,5,"En mi condicion de postulante/empleado, doy mi consentimiento a la realizacion de la prueba de alcohol: ".$nombreExamen,0,'J',0,5);$pdf->Ln(4);
        $pdf->MultiCell(168,5,"Se me ha interrogado sobre posibles enfermedades y sobre tratamientos medicos actuales que pudieran generar interpretaciones erroneas de los resultados.",0,'J',0,5);$pdf->Ln(4);
        $pdf->MultiCell(168,5,"Entiendo que la negacion a someterme a la prueba puede tener consecuencias negativas.",0,'J',0,5);
        $pdf->Ln(7);
        $y=$pdf->GetY();
        $pdf->Rect(22,$y,115,18);$pdf->Line(59,$y,59,$y+18);
        $pdf->Line(22,$y+6,137,$y+6);$pdf->Line(22,$y+12,137,$y+12);
        $pdf->Cell(37,5,"Fecha:",0,0,'R');
        $pdf->SetX(60);$pdf->Cell(0,5,$fecha,0,0,'L');$pdf->Ln(6);
        $pdf->Cell(37,5,"Apellido y Nombre:",0,0,'R');
        $pdf->SetX(60);$pdf->Cell(0,5,substr($paciente,0,30),0,0,'L');$pdf->Ln(7);
        $pdf->Cell(37,5,"DNI:",0,0,'R');
        $pdf->SetX(60);$pdf->Cell(0,5,$doc,0,0,'L');$pdf->Ln();
        //firma paciente
        $y=$pdf->GetY();$pdf->SetY($y);$y=$pdf->GetY();
        $pdf->Line(145,$y,195,$y);
        $pdf->SetXY(145,$y+2);$pdf->Cell(50,3,'Firma del Paciente',0,0,'C');
        $pdf->Ln(10);
        //negacion
        $y=$pdf->GetY();
        $pdf->Rect(22,$y,173,30);$pdf->SetFont('Arial','BU',11);
        $pdf->SetXY(23,$y+2);$pdf->Cell(0,5,'Negativa para Someterse a la Prueba de Drogas/Alcohol',0,0,'L');$pdf->Ln();
        $pdf->SetFont('Arial','',8);
        $pdf->SetX(23);$pdf->Cell(0,5,'Habiendo leido y entendido los parrafos 1-3, Yo expresamente me NIEGO a someterme a la prueba de deteccion de alcohol. ',0,0,'L');$pdf->Ln();
        $pdf->SetX(23);$pdf->Cell(0,5,'(Confirmar y ratificar la negativa firmando y poniendo su nombre inmediatamente abajo).',0,0,'L');$pdf->Ln(10);
        $pdf->SetX(23);$pdf->Cell(0,5,'Nombre del Empleado:',0,0,'L');
        $pdf->SetX(125);$pdf->Cell(0,5,'Firma del Paciente:',0,0,'L');
        $pdf->Ln(15);
        //temperatura muestra
        $y=$pdf->GetY();
        $pdf->Rect(22,$y,173,12);$pdf->Line(92,$y,92,$y+12);$pdf->Line(130,$y,130,$y+12);
        $pdf->Line(92,$y+6,195,$y+6);$pdf->SetFont('Arial','B',11);
        $pdf->SetXY(23,$y+5);$pdf->Cell(0,3,'Temperatura Recepcion de Muestra',0,0,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->SetXY(90,$y+2);$pdf->Cell(40,3,'Primer Muestra',0,0,'R');
        $pdf->SetXY(90,$y+8);$pdf->Cell(40,3,'Segunda Muestra',0,0,'R');
        $pdf->Ln();
        //firma paciente y tecnica
        $y=$pdf->GetY();$pdf->SetY($y+18);$y=$pdf->GetY();
        $pdf->Line(40,$y,90,$y);
        $pdf->SetXY(40,$y+2);$pdf->Cell(50,3,'Firma del Paciente',0,0,'C');
        $pdf->Line(125,$y,175,$y);
        $pdf->SetXY(125,$y+2);$pdf->Cell(50,3,'Firma Tecnica Laboratorio',0,0,'C');
        //cuadro resultados
        $pdf->Line(22,$y+12,195,$y+12);	$pdf->SetY($y+12);
        $pdf->Cell(0,10,"Resultado:",0,0,'L');$pdf->Ln();$y=$pdf->GetY();
        $pdf->Rect(22,$y,100,12);$pdf->Line(72,$y,72,$y+12);$pdf->Line(97,$y,97,$y+12);
        $pdf->Line(22,$y+6,122,$y+6);$pdf->SetFont('Arial','B',11);
        $pdf->SetXY(30,$y+2);$pdf->Cell(0,3,'',0,0,'L');
        $pdf->SetXY(72,$y+2);$pdf->Cell(25,3,'+',0,0,'C');
        $pdf->SetXY(97,$y+2);$pdf->Cell(25,3,'-',0,0,'C');
        $pdf->SetXY(23,$y+8);$pdf->Cell(0,3,'Alcohol',0,0,'L');$pdf->Ln();
        //firma doc
        $pdf->SetFont('Arial','',11);
        $y=$pdf->GetY();$y=$y+8;$pdf->SetY($y);
        $pdf->SetXY(22,$y);$pdf->Cell(0,5,"Nombre y apellido del Medico:",0,0,'L');
        $y=$y+5;$pdf->Line(145,$y,195,$y);
        $pdf->SetXY(145,$y+2);$pdf->Cell(50,3,'Firma',0,0,'C');
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
