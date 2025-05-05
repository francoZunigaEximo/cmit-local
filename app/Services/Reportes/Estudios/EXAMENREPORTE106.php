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

class EXAMENREPORTE106 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E106_1.jpg"),10,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E106_2.jpg"),10,155,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(50,36);$pdf->Cell(0,3,'CMIT de Irigoyen Miguel Antonio',0,0,'L');$pdf->SetXY(149,36);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(50,45);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        $pdf->SetXY(135,59);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(50,63);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');
        $pdf->SetXY(50,67);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(72,67);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(96,67);$pdf->Cell(0,3,$nac,0,0,'L');
        if($sexo=='Femenino'){$pdf->SetXY(164,67);}else{$pdf->SetXY(171,67);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(33,71);$pdf->Cell(0,3,substr($domipac,0,50),0,0,'L');
        $pdf->SetXY(39,75);$pdf->Cell(0,3,substr($locpac,0,40),0,0,'L');$pdf->SetXY(135,75);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(56,79);$pdf->Cell(0,3,substr($puesto,0,22),0,0,'L');$pdf->SetXY(173,79);$pdf->Cell(0,3,$antigpto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E106_3.jpg"),10,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E106_4.jpg"),10,147,190); 
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
