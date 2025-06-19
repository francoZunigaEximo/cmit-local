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

class EXAMENREPORTE154 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        include('banerlogo.php');
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);    
        //header
        $pdf->Rect(20,30,170,18);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetXY(20,32);$pdf->Cell(0,3,"Paciente: ",0,0,'L');$pdf->SetXY(150,32);$pdf->Cell(0,3,"Fecha: ",0,0,'L');
        $pdf->SetXY(20,37);$pdf->Cell(0,3,"Empresa: ",0,0,'L');$pdf->SetXY(150,37);$pdf->Cell(0,3,"Prestacion: ",0,0,'L');
        $pdf->SetXY(20,42);$pdf->Cell(0,3,"DNI: ",0,0,'L');	
        $pdf->SetFont('Arial','',9);
        $pdf->SetXY(36,32);$pdf->Cell(0,3,$paciente,0,0,'L');$pdf->SetXY(170,32);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(36,37);$pdf->Cell(0,3,$paraempresa,0,0,'L');$pdf->SetXY(170,37);$pdf->Cell(0,3,$idp,0,0,'L');
        $pdf->SetXY(36,42);$pdf->Cell(0,3,$doc,0,0,'L');$pdf->SetXY(170,42);$pdf->Cell(0,3,$edad,0,0,'L');
        //titulo
        $pdf->SetFont('Arial','BU',9);
        $pdf->SetXY(20,55);$pdf->Cell(0,3,$nombreExamen,0,0,'L');
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
