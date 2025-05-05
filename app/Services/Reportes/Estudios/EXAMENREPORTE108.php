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

class EXAMENREPORTE108 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        include('banerlogo.php');
        $pdf->SetFont('Arial','',8);$pdf->SetXY(10,30);$pdf->Cell(188,3,$fecha,0,0,'R');

        //nuevo
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetMargins(20,20,20); //left/top/right
        //titulo
        $pdf->SetFont('Arial','BU',12);$pdf->SetXY(10,37);$pdf->Cell(0,4,"CONSTANCIA DE RECONOCIMIENTO MEDICO",0,0,'C');$pdf->Ln(12);
        $y=$pdf->GetY();$y=$y+5;
        //cuerpo1
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(20,$y);$pdf->Cell(0,3,'EN EL DOMICILIO INDICADO',0,0,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->SetXY(150,$y-3);$pdf->Cell(0,3,'FECHA:',0,0,'L');$pdf->Line(165,$y,190,$y);$y=$y+6;
        $pdf->SetXY(20,$y);$pdf->Cell(0,3,'SE ENCONTRO EN EL DOMICLIO',0,0,'L');$pdf->Rect(100,$y,4,4);
        $pdf->SetXY(150,$y-3);$pdf->Cell(0,3,'HORA:',0,0,'L');$pdf->Line(165,$y,190,$y);$y=$y+6;
        $pdf->SetXY(20,$y);$pdf->Cell(0,3,'NO VIVE EN ESE DOMICILIO',0,0,'L');$pdf->Rect(100,$y,4,4);$y=$y+6;
        $pdf->SetXY(20,$y);$pdf->Cell(0,3,'NO SE ENCUENTRA EN SU DOMICLIO',0,0,'L');$pdf->Rect(100,$y,4,4);$y=$y+6;
        $pdf->SetXY(20,$y);$pdf->Cell(0,3,'DOMICILIO INEXISTENTE',0,0,'L');$pdf->Rect(100,$y,4,4);$y=$y+20;
        //cuerpo2 
        $pdf->SetXY(20,$y-3);$pdf->Cell(0,3,substr('DEJAMOS CONSTANCIA DE HABER REALIZADO EL RECONOCIMIENTO MEDICO DEL SR./A:',0,100),0,0,'L');$y=$y+8;
        $pdf->SetFont('Arial','B',9);$pdf->SetXY(20,$y-3);$pdf->Cell(0,3,substr($paciente,0,90),0,0,'L');
        $pdf->SetFont('Arial','',10);$pdf->SetXY(165,$y-3);$pdf->Cell(0,3,'DNI: ',0,0,'L');
        $pdf->SetFont('Arial','B',9);$pdf->SetXY(173,$y-3);$pdf->Cell(0,3,substr($doc,0,80),0,0,'L');$y=$y+8;
        $pdf->SetFont('Arial','',10);$pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'CON DOMICILIO EN CALLE: ',0,0,'L');
        $pdf->SetFont('Arial','B',9);$pdf->SetXY(67,$y-3);$pdf->Cell(0,3,substr($domipac,0,63),0,0,'L');$y=$y+8;
        $pdf->SetFont('Arial','',10);$pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'LOCALIDAD: ',0,0,'L');
        $pdf->SetFont('Arial','B',9);$pdf->SetXY(42,$y-3);$pdf->Cell(0,3,substr($locpac,0,23),0,0,'L');
        $pdf->SetFont('Arial','',10);$pdf->SetXY(89,$y-3);$pdf->Cell(0,3,'DE LA EMPRESA',0,0,'L');$pdf->Line(120,$y,190,$y);$y=$y+8;
        $pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'DEBE GUARDAR REPOSO ABSOLUTO/RELATIVO HASTA EL ',0,0,'L');$pdf->Line(123,$y,170,$y);
        $pdf->SetXY(170,$y-3);$pdf->Cell(0,3,'INCLUSIVE',0,0,'L');$y=$y+8;
        $pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'CITADO PARA EL CONTROL EL DIA',0,0,'L');$pdf->Line(82,$y,120,$y);
        $pdf->SetXY(121,$y-3);$pdf->Cell(0,3,'EN',0,0,'L');$pdf->Line(128,$y,160,$y);
        $pdf->SetXY(161,$y-3);$pdf->Cell(0,3,'A LAS',0,0,'L');$pdf->Line(172,$y,184,$y);
        $pdf->SetXY(184,$y-3);$pdf->Cell(0,3,'HS',0,0,'L');$y=$y+8;
        $pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'MEDICO DE CABECERA (QUE EXPIDIO EL CERTIFICADO): DR.',0,0,'L');$pdf->Line(126,$y,190,$y);$y=$y+8;
        $pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'LUGAR O NOSOCOMIO',0,0,'L');$pdf->Line(60,$y,99,$y);
        $pdf->SetXY(100,$y-3);$pdf->Cell(0,3,'MEDICACION',0,0,'L');$pdf->Line(124,$y,190,$y);$y=$y+8;
        $pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'DIAS DE REPOSO INDICADOS POR SU MEDICO:',0,0,'L');$pdf->Line(105,$y,190,$y);$y=$y+8;
        $pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'DEBE REINTEGRARSE A LAS TAREAS HABITUALES CON / SIN ADECUACION EL DIA:',0,0,'L');
        $pdf->Line(165,$y,190,$y);$y=$y+20;
        //obs
        $pdf->SetXY(20,$y-3);$pdf->Cell(0,3,'OBSERVACIONES',0,0,'L');$y=$y+8;
        $pdf->Line(20,$y,190,$y);$y=$y+8;
        $pdf->Line(20,$y,190,$y);$y=$y+8;
        //firmas
        $pdf->SetFont('Arial','',8);
        $pdf->Line(20,243,75,243);
        $pdf->SetXY(20,245);$pdf->Cell(50,3,'FIRMA DEL MEDICO',0,0,'C');
        $pdf->Line(130,243,185,243);
        $pdf->SetXY(130,245);$pdf->Cell(50,3,'NOTIFICADO:',0,0,'C');
        $pdf->SetXY(130,250);$pdf->Cell(55,3,'FIRMA DEL EMPLEADO O RESPONSABLE',0,0,'C');
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
