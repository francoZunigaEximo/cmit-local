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

class EXAMENREPORTE77 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
       
        $pdf->Image(public_path("/archivos/reportes/E77_1.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E77_2.jpg"),12,155,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(37,77);$pdf->Cell(0,3,'Neuquen',0,0,'L');$pdf->SetXY(152,77);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(37,92);$pdf->Cell(0,3,substr($paraempresa,0,70),0,0,'L');
        $pdf->SetXY(42,137);$pdf->Cell(0,3,substr($paciente,0,70),0,0,'L');
        $pdf->SetXY(37,142);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(45,146);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(83,146);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(180,146);}else{$pdf->SetXY(186,146);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(45,151);$pdf->Cell(0,3,$puesto,0,0,'L');$pdf->SetXY(135,151);$pdf->Cell(0,3,$antigpto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E77_3.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E77_4.jpg"),12,152,190); 
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
