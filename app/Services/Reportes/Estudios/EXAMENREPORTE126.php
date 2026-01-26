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

class EXAMENREPORTE126 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E126_1.jpg"),10,17,200); 
        $pdf->Image(public_path("/archivos/reportes/E126_2.jpg"),10,150,200); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //datos
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(22,30);$pdf->Cell(0,3,'SALUD OCUPACIONAL SRL',0,0,'L');
        $pdf->SetXY(88,30);$pdf->Cell(0,3,'JUAN B. JUSTO 825 - NEUQUEN',0,0,'L');
        $pdf->SetXY(166,30);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(22,38);$pdf->Cell(0,3,substr($paraempresa,0,70),0,0,'L');
        $pdf->SetXY(165,38);$pdf->Cell(0,3,$cuit,0,0,'L');
        $pdf->SetXY(22,57);$pdf->Cell(0,3,substr($paciente,0,25),0,0,'L');
        $pdf->SetXY(71,57);$pdf->Cell(0,3,$tipodoc,0,0,'L');
        $pdf->SetXY(82,57);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(121,57);$pdf->Cell(0,3,substr($lugarnac,0,20),0,0,'L');
        $pdf->SetXY(166,57);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(25,68);$pdf->Cell(0,3,$cuil,0,0,'L');
        $pdf->SetXY(54,67);$pdf->Cell(0,3,$puestoestudio,0,0,'L');
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
