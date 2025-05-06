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

class EXAMENREPORTE153 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E153_1.jpg"),5,20,195); 
        $pdf->Image(public_path("/archivos/reportes/E153_2.jpg"),5,124,195); 
        $pdf->Image(public_path("/archivos/reportes/E153_3.jpg"),5,215,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");    
        //datos	
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(29,39);$pdf->Cell(0,3,'SALUD OCUPACIONAL SRL',0,0,'L');
        $pdf->SetXY(160,39);$pdf->Cell(0,3,$fecha,0,0,'L');
        //
        $pdf->SetXY(28,52);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        //
        $pdf->SetXY(140,70);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(36,75);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(42,79);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(75,79);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(112,79);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(184,79);}else{$pdf->SetXY(192,79);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(18,83);$pdf->Cell(0,3,substr($domipac,0,60),0,0,'L');
        $pdf->SetXY(26,87);$pdf->Cell(0,3,substr($locpac,0,40),0,0,'L');
        $pdf->SetXY(115,87);$pdf->Cell(0,3,substr($cp,0,40),0,0,'L');
        $pdf->SetXY(42,91);$pdf->Cell(0,3,substr($puesto,0,30),0,0,'L');
        $pdf->SetXY(180,91);$pdf->Cell(0,3,$antigpto,0,0,'L');
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
