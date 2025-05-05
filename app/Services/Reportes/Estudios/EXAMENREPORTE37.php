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

class EXAMENREPORTE37 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E37.jpg"),25,20,147);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(105,88);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(43,95);$pdf->Cell(0,3,$paraempresa,0,0,'L');
        $pdf->SetXY(54,119);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(50,124);$pdf->Cell(0,3,$doc,0,0,'L');
        if($sexo=='Masculino'){$pdf->SetXY(53,130);}else{$pdf->SetXY(62,130);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(55,134);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(50,139);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(60,144);$pdf->Cell(0,3,$antig,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E37_1.jpg"),25,30,147);
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
