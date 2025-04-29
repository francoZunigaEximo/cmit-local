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

class EXAMENREPORTE35 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        $pdf->SetMargins(22,20,22);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetXY(22,32);$pdf->SetFont('Arial','BU',12);$pdf->Cell(0,10,"Centro de Medicina Integral del Trabajo",0,0,'L');
        $pdf->SetXY(22,40);$pdf->SetFont('Arial','I',12);$pdf->Cell(0,8,"Solicitud Licencia de Conducir ",0,0,'L');$pdf->Ln(12);
        $pdf->SetFont('Arial','',10);
        $pdf->SetXY(22,53);$pdf->Cell(0,8,"Nombre y Apellido: ".$paciente,0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"DNI: ".$doc,0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Fecha de Nacimiento: ".$fechanac.'   '.$edad,0,0,'L');$pdf->Ln(20);
        $pdf->Cell(0,10,"Categoria solicitada: ",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Examen fisico: ",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Examen visual: ",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Observaciones: ",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Vigencia: ",0,0,'L');$pdf->Ln();
        $pdf->Cell(0,10,"Fecha: ".$fecha,0,0,'L');$pdf->Ln(30);
        $pdf->Cell(0,10,"Firma  profesional: ",0,0,'L');$pdf->Ln(20);
        $pdf->Cell(0,10,"Firma solicitante: ",0,0,'L');$pdf->Ln();
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E35.jpg"),25,20,167);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetXY(30,49);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(130,49);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(163,49);$pdf->Cell(0,4,$fecha,0,0,'L');
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E35_1.jpg"),25,30,167);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E35_2.jpg"),25,30,167);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
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
