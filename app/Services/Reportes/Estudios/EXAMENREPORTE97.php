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

class EXAMENREPORTE97 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');

        include ("banerlogo.php");

        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $y=29;$pdf->SetXY(10,$y+3);$pdf->SetFont('Arial','B',12);$pdf->Cell(200,5,'ELECTROENCEFALOGRAMA',0,0,'C');
        $pdf->SetFont('Arial','',12);$pdf->Rect(14,$y+15,169,74); 
        $pdf->SetXY(15,$y+18);$pdf->Cell(0,3,'Fecha: '.$fecha,0,0,'L');$pdf->SetXY(120,$y+18);$pdf->Cell(0,3,'Prestacion: '.$idp,0,0,'L');
        $pdf->Line(14,$y+26,183,$y+26);
        $pdf->SetXY(15,$y+32);$pdf->Cell(0,3,'Paciente: '.substr($paciente,0,50),0,0,'L');
        $pdf->SetXY(15,$y+39);$pdf->Cell(0,3,'DNI: '.$doc,0,0,'L');$pdf->SetXY(120,$y+39);$pdf->Cell(0,3,'Sexo: '.$sexo,0,0,'L');
        $pdf->SetXY(15,$y+46);$pdf->Cell(0,3,'Fecha Nac: '.$fechanac,0,0,'L');$pdf->SetXY(120,$y+46);$pdf->Cell(0,3,'Edad: '.$edad,0,0,'L');
        $pdf->Line(14,$y+54,183,$y+54);
        $pdf->SetXY(15,$y+60);$pdf->Cell(0,3,'Cliente: '.substr($rsempresa,0,50),0,0,'L');
        $pdf->SetXY(15,$y+67);$pdf->Cell(0,3,'Empresa: '.substr($paraempresa,0,45),0,0,'L');
        $pdf->SetXY(15,$y+74);$pdf->Cell(0,3,'Puesto: '.substr($puesto,0,15),0,0,'L');
        $pdf->SetXY(15,$y+81);$pdf->Cell(0,3,'Antiguedad: '.$antig,0,0,'L');
        //ecg
        $texto= "Registro de Vigilia: Organizado y reactivo. Se realiza apertura y cierre ocular e hiperventilacion. No se registran grafoelementos epileptiformes. Registro normal para la edad y estado actual del paciente.";
        $pdf->SetXY(11,130);$pdf->MultiCell(170,6,$texto,0,'J',0,5);
        //pie
        $pdf->SetFont('Arial','B',9);
        $pdf->Line(130,250,185,250);
        $pdf->SetXY(130,252);$pdf->Cell(55,3,'FIRMA Y SELLO DEL PROFESIONAL',0,0,'C');
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
